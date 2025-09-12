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
          'jenis_pembayaran', // spp_only, mixed, all_types
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
               $label = $this->determineLabel($siswa, $features);

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

          // 1. Ketepatan Waktu - dengan logika yang diperbaiki
          $tepatWaktu = 0;
          $terlambat = 0;
          $totalPembayaran = 0;
          $awalWaktu = 0; // Pembayaran yang dilakukan jauh sebelum deadline

          foreach ($tagihan as $t) {
               if ($t->status === 'sudah_bayar') {
                    $pembayaran = $t->pembayaran()->where('status_konfirmasi', 'confirmed')->first();
                    if ($pembayaran) {
                         $totalPembayaran++;
                         $daysDiff = $pembayaran->tanggal_bayar->diffInDays($t->deadline, false);
                         
                         if ($daysDiff > 0) { // Bayar sebelum deadline
                              if ($daysDiff > 7) {
                                   $awalWaktu++; // Bayar lebih dari seminggu sebelum deadline
                              } else {
                                   $tepatWaktu++; // Bayar mendekati deadline tapi masih tepat
                              }
                         } else {
                              $terlambat++; // Bayar setelah deadline
                         }
                    }
               }
          }

          if ($totalPembayaran === 0) {
               return null;
          }

          // Logika klasifikasi ketepatan waktu yang lebih baik
          $persentaseAwal = ($awalWaktu / $totalPembayaran) * 100;
          $persentaseTepatWaktu = (($tepatWaktu + $awalWaktu) / $totalPembayaran) * 100;

          $ketepatanWaktu = 'late';
          if ($persentaseAwal >= 50) {
               $ketepatanWaktu = 'early';
          } elseif ($persentaseTepatWaktu >= 70) {
               $ketepatanWaktu = 'ontime';
          }

          // 2. Frekuensi Bayar - dengan perhitungan yang lebih akurat
          $totalTagihan = $tagihan->count();
          $persentaseBayar = ($totalPembayaran / $totalTagihan) * 100;

          $frekuensiBayar = 'low';
          if ($persentaseBayar >= 85) {
               $frekuensiBayar = 'high';
          } elseif ($persentaseBayar >= 60) {
               $frekuensiBayar = 'medium';
          }

          // 3. Jenis Pembayaran - dengan kategori yang disederhanakan
          $jenisBayar = $tagihan->where('status', 'sudah_bayar')
               ->pluck('jenisPembayaran.nama_pembayaran')
               ->unique()
               ->sort()
               ->values()
               ->toArray();

          $jenisPembayaran = 'spp_only';
          if (count($jenisBayar) >= 3) {
               $jenisPembayaran = 'all_types';
          } elseif (count($jenisBayar) >= 2) {
               $jenisPembayaran = 'mixed';
          }

          // 4. Kelas - diperbaiki untuk menangani format yang beragam
          $kelasRaw = $siswa->kelas;
          $kelas = 'X';
          if (strpos($kelasRaw, 'XII') !== false || strpos($kelasRaw, '12') !== false) {
               $kelas = 'XII';
          } elseif (strpos($kelasRaw, 'XI') !== false || strpos($kelasRaw, '11') !== false) {
               $kelas = 'XI';
          } elseif (strpos($kelasRaw, 'X') !== false || strpos($kelasRaw, '10') !== false) {
               $kelas = 'X';
          }

          return [
               'ketepatan_waktu' => $ketepatanWaktu,
               'frekuensi_bayar' => $frekuensiBayar,
               'jenis_pembayaran' => $jenisPembayaran,
               'kelas' => $kelas
          ];
     }

     protected function determineLabel(Siswa $siswa, $features)
     {
          if (!$features) {
               return null;
          }

          // Logic yang diperbaiki untuk menentukan label
          $ketepatanWaktu = $features['ketepatan_waktu'];
          $frekuensiBayar = $features['frekuensi_bayar'];
          $jenisPembayaran = $features['jenis_pembayaran'];

          // Skor untuk setiap kategori (0-3)
          $skorDisiplin = 0;
          $skorTerlambat = 0;
          $skorSelektif = 0;

          // Evaluasi ketepatan waktu
          if ($ketepatanWaktu === 'early') {
               $skorDisiplin += 3;
          } elseif ($ketepatanWaktu === 'ontime') {
               $skorDisiplin += 2;
               $skorSelektif += 1;
          } else {
               $skorTerlambat += 3;
          }

          // Evaluasi frekuensi bayar
          if ($frekuensiBayar === 'high') {
               $skorDisiplin += 3;
          } elseif ($frekuensiBayar === 'medium') {
               $skorDisiplin += 1;
               $skorSelektif += 2;
          } else {
               $skorTerlambat += 3;
               $skorSelektif += 1;
          }

          // Evaluasi jenis pembayaran
          if ($jenisPembayaran === 'all_types') {
               $skorDisiplin += 3;
          } elseif ($jenisPembayaran === 'mixed') {
               $skorDisiplin += 1;
               $skorSelektif += 2;
          } else {
               $skorSelektif += 3;
               $skorTerlambat += 1;
          }

          // Tentukan label berdasarkan skor tertinggi
          $maxSkor = max($skorDisiplin, $skorTerlambat, $skorSelektif);
          
          if ($skorDisiplin === $maxSkor) {
               return 'pembayar_disiplin';
          } elseif ($skorSelektif === $maxSkor) {
               return 'pembayar_selektif';
          } else {
               return 'pembayar_terlambat';
          }
     }

     public function trainModel($trainingData)
     {
          // Hitung prior probability untuk setiap class dengan Laplace smoothing
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
               // Laplace smoothing untuk prior probability
               $priorProbabilities[$class] = ($classCount[$class] + 1) / ($totalSamples + count($this->classes));
          }

          // Hitung likelihood untuk setiap feature dan value dengan Laplace smoothing
          $likelihoods = [];
          $featureValues = [];

          // Kumpulkan semua possible values untuk setiap feature
          foreach ($this->features as $feature) {
               $featureValues[$feature] = [];
               foreach ($trainingData as $data) {
                    $value = $data['features'][$feature];
                    if (!in_array($value, $featureValues[$feature])) {
                         $featureValues[$feature][] = $value;
                    }
               }
          }

          foreach ($this->classes as $class) {
               $likelihoods[$class] = [];
               $classData = array_filter($trainingData, function($data) use ($class) {
                    return $data['label'] === $class;
               });
               $classSize = count($classData);

               foreach ($this->features as $feature) {
                    $likelihoods[$class][$feature] = [];
                    $possibleValues = $featureValues[$feature];
                    $vocabularySize = count($possibleValues);

                    foreach ($possibleValues as $value) {
                         // Hitung jumlah kemunculan value untuk class ini
                         $count = 0;
                         foreach ($classData as $data) {
                              if ($data['features'][$feature] === $value) {
                                   $count++;
                              }
                         }

                         // Laplace smoothing
                         $likelihoods[$class][$feature][$value] = ($count + 1) / ($classSize + $vocabularySize);
                    }
               }
          }

          return [
               'prior_probabilities' => $priorProbabilities,
               'likelihoods' => $likelihoods,
               'feature_values' => $featureValues,
               'training_data_count' => $totalSamples
          ];
     }

     public function predict($features, $model)
     {
          $posteriorProbabilities = [];

          foreach ($this->classes as $class) {
               $probability = log($model['prior_probabilities'][$class]);

               foreach ($this->features as $feature) {
                    $featureValue = $features[$feature];

                    // Use likelihood if exists, otherwise use very small probability
                    if (isset($model['likelihoods'][$class][$feature][$featureValue])) {
                         $likelihood = $model['likelihoods'][$class][$feature][$featureValue];
                    } else {
                         // Laplace smoothing untuk unseen values
                         $vocabularySize = count($model['feature_values'][$feature]);
                         $likelihood = 1 / ($vocabularySize + 1);
                    }

                    $probability += log($likelihood);
               }

               $posteriorProbabilities[$class] = $probability;
          }

          // Convert log probabilities back to normal probabilities
          $maxLogProb = max($posteriorProbabilities);
          $normalizedProbs = [];
          $totalProb = 0;

          foreach ($posteriorProbabilities as $class => $logProb) {
               $normalizedProbs[$class] = exp($logProb - $maxLogProb);
               $totalProb += $normalizedProbs[$class];
          }

          // Normalize to get actual probabilities
          foreach ($normalizedProbs as $class => $prob) {
               $normalizedProbs[$class] = $prob / $totalProb;
          }

          // Return class with highest probability
          $predictedClass = array_keys($normalizedProbs, max($normalizedProbs))[0];
          $confidence = $normalizedProbs[$predictedClass];

          return [
               'predicted_class' => $predictedClass,
               'confidence' => $confidence,
               'probabilities' => $normalizedProbs
          ];
     }

     public function classifyAllStudents()
     {
          // Prepare training data
          $trainingData = $this->prepareTrainingData();

          if (count($trainingData) < 5) {
               throw new \Exception('Tidak cukup data untuk training. Minimal 5 data diperlukan. Saat ini hanya ' . count($trainingData) . ' data tersedia.');
          }

          // Train model
          $model = $this->trainModel($trainingData);

          // Debug: Log distribution of training data
          $trainingDistribution = [];
          foreach ($this->classes as $class) {
               $trainingDistribution[$class] = count(array_filter($trainingData, function($data) use ($class) {
                    return $data['label'] === $class;
               }));
          }

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
                                   'probabilities' => $prediction['probabilities'],
                                   'training_distribution' => $trainingDistribution
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
                    'training_distribution' => $trainingDistribution,
                    'prior_probabilities' => $model['prior_probabilities']
               ],
               'classifications' => $results
          ];
     }
}