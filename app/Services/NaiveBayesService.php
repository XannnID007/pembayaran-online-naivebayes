<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\KlasifikasiSiswa;
use Carbon\Carbon;

class NaiveBayesService
{
     protected $classes = [
          'pembayar_disiplin',
          'pembayar_terlambat',
          'pembayar_selektif'
     ];

     protected $features = [
          'ketepatan_waktu', // early, ontime, late
          'frekuensi_bayar',  // high, medium, low
          'jenis_pembayaran', // spp_only, uts_only, uas_only, spp_uts, spp_uas, uts_uas, all_types
          'kelas'            // X, XI, XII
     ];

     public function getClassificationReport()
     {
          try {
               $classifications = KlasifikasiSiswa::with('siswa')
                    ->where('tanggal_prediksi', '>=', Carbon::now()->subDays(30))
                    ->get();

               $report = [
                    'total_classified' => $classifications->count(),
                    'distribution' => [],
                    'average_confidence' => 0,
                    'recent_classifications' => $classifications->take(10)
               ];

               // Calculate distribution
               foreach ($this->classes as $class) {
                    $count = $classifications->where('kategori_prediksi', $class)->count();
                    $report['distribution'][$class] = [
                         'count' => $count,
                         'percentage' => $classifications->count() > 0 ? round(($count / $classifications->count()) * 100, 2) : 0
                    ];
               }

               // Calculate average confidence
               if ($classifications->count() > 0) {
                    $report['average_confidence'] = round($classifications->avg('confidence_score'), 4);
               }

               return $report;
          } catch (\Exception $e) {
               // Return default empty report if error
               return [
                    'total_classified' => 0,
                    'distribution' => [
                         'pembayar_disiplin' => ['count' => 0, 'percentage' => 0],
                         'pembayar_terlambat' => ['count' => 0, 'percentage' => 0],
                         'pembayar_selektif' => ['count' => 0, 'percentage' => 0],
                    ],
                    'average_confidence' => 0,
                    'recent_classifications' => collect([])
               ];
          }
     }

     public function prepareTrainingData()
     {
          $trainingData = [];
          $siswaList = Siswa::with(['tagihan.pembayaran', 'tagihan.jenisPembayaran'])->get();

          foreach ($siswaList as $siswa) {
               $features = $this->extractFeatures($siswa);
               $label = $this->determineLabel($siswa);

               if ($features && $label) {
                    $trainingData[] = [
                         'features' => $features,
                         'label' => $label
                    ];
               }
          }

          return $trainingData;
     }

     protected function extractFeatures(Siswa $siswa)
     {
          $tagihan = $siswa->tagihan;

          if ($tagihan->count() < 3) {
               return null; // Tidak cukup data untuk analisis
          }

          // 1. Ketepatan Waktu
          $tepatWaktu = 0;
          $terlambat = 0;
          $totalPembayaran = 0;

          foreach ($tagihan as $t) {
               if ($t->status === 'sudah_bayar') {
                    $pembayaran = $t->pembayaran()->where('status_konfirmasi', 'confirmed')->first();
                    if ($pembayaran) {
                         $totalPembayaran++;
                         if ($pembayaran->tanggal_bayar <= $t->deadline) {
                              $tepatWaktu++;
                         } else {
                              $terlambat++;
                         }
                    }
               }
          }

          if ($totalPembayaran === 0) {
               return null;
          }

          $persentaseTepatWaktu = ($tepatWaktu / $totalPembayaran) * 100;

          $ketepatanWaktu = 'late';
          if ($persentaseTepatWaktu >= 80) {
               $ketepatanWaktu = 'early';
          } elseif ($persentaseTepatWaktu >= 50) {
               $ketepatanWaktu = 'ontime';
          }

          // 2. Frekuensi Bayar
          $totalTagihan = $tagihan->count();
          $persentaseBayar = ($totalPembayaran / $totalTagihan) * 100;

          $frekuensiBayar = 'low';
          if ($persentaseBayar >= 80) {
               $frekuensiBayar = 'high';
          } elseif ($persentaseBayar >= 50) {
               $frekuensiBayar = 'medium';
          }

          // 3. Jenis Pembayaran
          $jenisBayar = $tagihan->where('status', 'sudah_bayar')
               ->pluck('jenisPembayaran.nama_pembayaran')
               ->unique()
               ->sort()
               ->values()
               ->toArray();

          $jenisPembayaran = 'spp_only';
          if (count($jenisBayar) === 3) {
               $jenisPembayaran = 'all_types';
          } elseif (count($jenisBayar) === 2) {
               sort($jenisBayar);
               $jenisPembayaran = strtolower(implode('_', $jenisBayar));
          } elseif (count($jenisBayar) === 1) {
               $jenisPembayaran = strtolower($jenisBayar[0]) . '_only';
          }

          // 4. Kelas
          $kelas = substr($siswa->kelas, 0, strpos($siswa->kelas, ' ') ?: strlen($siswa->kelas));

          return [
               'ketepatan_waktu' => $ketepatanWaktu,
               'frekuensi_bayar' => $frekuensiBayar,
               'jenis_pembayaran' => $jenisPembayaran,
               'kelas' => $kelas
          ];
     }

     protected function determineLabel(Siswa $siswa)
     {
          $features = $this->extractFeatures($siswa);

          if (!$features) {
               return null;
          }

          // Logic untuk menentukan label berdasarkan kombinasi features
          $ketepatanWaktu = $features['ketepatan_waktu'];
          $frekuensiBayar = $features['frekuensi_bayar'];
          $jenisPembayaran = $features['jenis_pembayaran'];

          // Pembayar Disiplin: Tepat waktu + frekuensi tinggi + bayar semua jenis
          if ($ketepatanWaktu === 'early' && $frekuensiBayar === 'high' && in_array($jenisPembayaran, ['all_types', 'spp_uts', 'spp_uas'])) {
               return 'pembayar_disiplin';
          }

          // Pembayar Terlambat: Sering terlambat + frekuensi rendah
          if ($ketepatanWaktu === 'late' || $frekuensiBayar === 'low') {
               return 'pembayar_terlambat';
          }

          // Pembayar Selektif: Hanya bayar jenis tertentu
          if (in_array($jenisPembayaran, ['spp_only', 'uts_only', 'uas_only'])) {
               return 'pembayar_selektif';
          }

          // Default ke terlambat jika tidak masuk kategori lain
          return 'pembayar_terlambat';
     }

     public function trainModel($trainingData)
     {
          // Hitung prior probability untuk setiap class
          $classCount = [];
          $totalSamples = count($trainingData);

          foreach ($this->classes as $class) {
               $classCount[$class] = 0;
          }

          foreach ($trainingData as $data) {
               $classCount[$data['label']]++;
          }

          $priorProbabilities = [];
          foreach ($this->classes as $class) {
               $priorProbabilities[$class] = $classCount[$class] / $totalSamples;
          }

          // Hitung likelihood untuk setiap feature dan value
          $likelihoods = [];

          foreach ($this->classes as $class) {
               $likelihoods[$class] = [];

               foreach ($this->features as $feature) {
                    $likelihoods[$class][$feature] = [];

                    // Get all possible values for this feature
                    $featureValues = [];
                    foreach ($trainingData as $data) {
                         if ($data['label'] === $class) {
                              $featureValues[] = $data['features'][$feature];
                         }
                    }

                    // Count occurrences
                    $valueCounts = array_count_values($featureValues);
                    $totalForClass = count($featureValues);

                    // Calculate probabilities with Laplace smoothing
                    foreach ($valueCounts as $value => $count) {
                         $likelihoods[$class][$feature][$value] = ($count + 1) / ($totalForClass + 1);
                    }
               }
          }

          return [
               'prior_probabilities' => $priorProbabilities,
               'likelihoods' => $likelihoods,
               'training_data_count' => $totalSamples
          ];
     }

     public function predict($features, $model)
     {
          $posteriorProbabilities = [];

          foreach ($this->classes as $class) {
               $probability = $model['prior_probabilities'][$class];

               foreach ($this->features as $feature) {
                    $featureValue = $features[$feature];

                    // Use likelihood if exists, otherwise use small probability (Laplace smoothing)
                    if (isset($model['likelihoods'][$class][$feature][$featureValue])) {
                         $probability *= $model['likelihoods'][$class][$feature][$featureValue];
                    } else {
                         $probability *= 0.01; // Small probability for unseen values
                    }
               }

               $posteriorProbabilities[$class] = $probability;
          }

          // Normalize probabilities
          $total = array_sum($posteriorProbabilities);
          if ($total > 0) {
               foreach ($posteriorProbabilities as $class => $prob) {
                    $posteriorProbabilities[$class] = $prob / $total;
               }
          }

          // Return class with highest probability
          $predictedClass = array_keys($posteriorProbabilities, max($posteriorProbabilities))[0];
          $confidence = $posteriorProbabilities[$predictedClass];

          return [
               'predicted_class' => $predictedClass,
               'confidence' => $confidence,
               'probabilities' => $posteriorProbabilities
          ];
     }

     public function classifyAllStudents()
     {
          // Prepare training data
          $trainingData = $this->prepareTrainingData();

          if (count($trainingData) < 10) {
               throw new \Exception('Tidak cukup data untuk training. Minimal 10 data diperlukan. Saat ini hanya ' . count($trainingData) . ' data tersedia.');
          }

          // Train model
          $model = $this->trainModel($trainingData);

          // Classify all students
          $siswaList = Siswa::with(['tagihan.pembayaran', 'tagihan.jenisPembayaran'])->get();
          $results = [];

          foreach ($siswaList as $siswa) {
               $features = $this->extractFeatures($siswa);

               if ($features) {
                    $prediction = $this->predict($features, $model);

                    // Save or update classification
                    KlasifikasiSiswa::updateOrCreate(
                         ['siswa_id' => $siswa->id],
                         [
                              'kategori_prediksi' => $prediction['predicted_class'],
                              'confidence_score' => $prediction['confidence'],
                              'tanggal_prediksi' => Carbon::now(),
                              'detail_analisis' => [
                                   'features' => $features,
                                   'probabilities' => $prediction['probabilities']
                              ]
                         ]
                    );

                    $results[] = [
                         'siswa' => $siswa,
                         'prediction' => $prediction,
                         'features' => $features
                    ];
               }
          }

          return [
               'model_info' => [
                    'training_samples' => count($trainingData),
                    'prior_probabilities' => $model['prior_probabilities']
               ],
               'classifications' => $results
          ];
     }
}
