<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('category')
            ->where('user_id', Auth::user()->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(5);

        return view('transaksi.index', compact('transactions'));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::user()->id)
            ->whereNotIn('nama_kategori', ['Tabungan', 'Struk'])
            ->get();

        return view('transaksi.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'       => 'required|string|max:255',
            'tipe'        => 'required|in:pemasukan,pengeluaran',
            'category_id' => [
                'required',
                // Pastikan kategori ada dan milik user yang sedang login (Cegah IDOR / Inspect Element Hack)
                function ($attribute, $value, $fail) {
                    $exists = Category::where('id', $value)->where('user_id', Auth::user()->id)->exists();
                    if (!$exists) {
                        $fail('Kategori yang dipilih tidak valid atau bukan milik Anda.');
                    }
                },
            ],
            // Mencegah nilai angka terlalu besar hingga error database (max 999 Miliar)
            'jumlah'      => 'required|integer|min:1|max:999999999999',
            // Mencegah input transaksi hari esok/masa depan
            'tanggal'     => 'required|date|before_or_equal:today',
            // Keterangan wajib string dan panjang wajar agar hemat database
            'keterangan'  => 'nullable|string|max:1000'
        ]);

        $user_id = Auth::user()->id;
        $notif_pengeluaran = null;
        
        // Cek saldo kalau dia pengeluaran
        if ($request->tipe == 'pengeluaran') {
            $total_masuk = Transaction::where('user_id', $user_id)->where('tipe', 'pemasukan')->sum('jumlah');
            $total_keluar = Transaction::where('user_id', $user_id)->where('tipe', 'pengeluaran')->sum('jumlah');

            $tanggal = Carbon::parse($request->tanggal);
            $awal_bulan = $tanggal->copy()->startOfMonth()->toDateString();
            $akhir_bulan = $tanggal->copy()->endOfMonth()->toDateString();

            $total_keluar_bulan_ini = Transaction::where('user_id', $user_id)
                ->where('tipe', 'pengeluaran')
                ->whereBetween('tanggal', [$awal_bulan, $akhir_bulan])
                ->sum('jumlah');

            $kelipatan_lama = floor($total_keluar_bulan_ini / 1000000);
            $kelipatan_baru = floor(($total_keluar_bulan_ini + $request->jumlah) / 1000000);

            if ($kelipatan_baru > $kelipatan_lama && $kelipatan_baru > 0) {
                $rupiah = number_format($kelipatan_baru * 1000000, 0, ',', '.');
                $notif_pengeluaran = "Peringatan: Total pengeluaran Anda bulan ini telah mencapai Rp {$rupiah}. Harap perhatikan keuangan Anda!";
            }

            $sisa_saldo = $total_masuk - $total_keluar;

            if ($sisa_saldo < $request->jumlah) {
                return redirect()->back()->withInput()->with('error', 'Saldo pemasukan tidak cukup untuk melakukan pengeluaran!');
            }

            // Potong saldo di kategori pemasukan
            $this->potongSaldoKategoriPemasukan($user_id, $request->jumlah);
        }

        // Kalau pemasukan, tambah ke kategori pemasukan
        if ($request->tipe == 'pemasukan') {
            $kategori = Category::find($request->category_id);
            if ($kategori) {
                $kategori->saldo = $kategori->saldo + $request->jumlah;
                $kategori->save();
            }
        }

        $transaksi = new Transaction();
        $transaksi->user_id = $user_id;
        $transaksi->judul = $request->judul;
        $transaksi->tipe = $request->tipe;
        $transaksi->category_id = $request->category_id;
        $transaksi->jumlah = $request->jumlah;
        $transaksi->tanggal = $request->tanggal;
        $transaksi->keterangan = $request->keterangan;
        $transaksi->save();

        if ($notif_pengeluaran != null) {
            return redirect()->route('transaksi.index')
                ->with('success', 'Transaksi berhasil ditambahkan.')
                ->with('warning', $notif_pengeluaran);
        }

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function show($id)
    {
        $transaksi = Transaction::find($id);

        if ($transaksi->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        return view('transaksi.show', compact('transaksi'));
    }

    public function edit($id)
    {
        $transaksi = Transaction::find($id);

        if ($transaksi->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        $categories = Category::where('user_id', Auth::user()->id)
            ->whereNotIn('nama_kategori', ['Tabungan', 'Struk'])
            ->get();

        return view('transaksi.edit', compact('transaksi', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaction::find($id);

        if ($transaksi->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        $request->validate([
            'judul'       => 'required|string|max:255',
            'tipe'        => 'required|in:pemasukan,pengeluaran',
            'category_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Category::where('id', $value)->where('user_id', Auth::user()->id)->exists();
                    if (!$exists) {
                        $fail('Kategori yang dipilih tidak valid atau bukan milik Anda.');
                    }
                },
            ],
            'jumlah'      => 'required|integer|min:1|max:999999999999',
            'tanggal'     => 'required|date|before_or_equal:today',
            'keterangan'  => 'nullable|string|max:1000'
        ]);

        $user_id = Auth::user()->id;
        $notif_pengeluaran = null;

        // Kembalikan saldo yang lama dulu
        if ($transaksi->tipe == 'pemasukan') {
            $kategori_lama = Category::find($transaksi->category_id);
            if ($kategori_lama) {
                $kategori_lama->saldo = $kategori_lama->saldo - $transaksi->jumlah;
                $kategori_lama->save();
            }
        } else {
            // Kalau dlu pengeluaran, kembalikan saldonya ke salah satu kategori pemasukan
            $kategori_balik = Category::where('user_id', $user_id)->where('warna', 'success')->orderBy('id', 'desc')->first();
            if ($kategori_balik) {
                $kategori_balik->saldo = $kategori_balik->saldo + $transaksi->jumlah;
                $kategori_balik->save();
            }
        }

        // Cek saldo kalau yg baru adalah pengeluaran
        if ($request->tipe == 'pengeluaran') {
            // Hitung ulang saldo dgn transaksi ini dianggap 0 dulu (udah dicancel diatas / exclude)
            $total_masuk = Transaction::where('user_id', $user_id)->where('tipe', 'pemasukan')->where('id', '!=', $transaksi->id)->sum('jumlah');
            $total_keluar = Transaction::where('user_id', $user_id)->where('tipe', 'pengeluaran')->where('id', '!=', $transaksi->id)->sum('jumlah');

            $tanggal = Carbon::parse($request->tanggal);
            $awal_bulan = $tanggal->copy()->startOfMonth()->toDateString();
            $akhir_bulan = $tanggal->copy()->endOfMonth()->toDateString();

            $total_keluar_bulan_ini = Transaction::where('user_id', $user_id)
                ->where('tipe', 'pengeluaran')
                ->where('id', '!=', $transaksi->id)
                ->whereBetween('tanggal', [$awal_bulan, $akhir_bulan])
                ->sum('jumlah');

            $kelipatan_lama = floor($total_keluar_bulan_ini / 1000000);
            $kelipatan_baru = floor(($total_keluar_bulan_ini + $request->jumlah) / 1000000);

            if ($kelipatan_baru > $kelipatan_lama && $kelipatan_baru > 0) {
                $rupiah = number_format($kelipatan_baru * 1000000, 0, ',', '.');
                $notif_pengeluaran = "Peringatan: Total pengeluaran Anda bulan ini telah mencapai Rp {$rupiah}. Harap perhatikan keuangan Anda!";
            }

            // + kalo dia asalnya pemasukan, berarti tadi saldo belum berkurang di DB transaksi
            $sisa_saldo = $total_masuk - $total_keluar;

            if ($sisa_saldo < $request->jumlah) {
                // Balikin lg kategori yg lama kalau tadi sempet dikurangin / ditambah
                if ($transaksi->tipe == 'pemasukan') {
                    $kategori_lama = Category::find($transaksi->category_id);
                    if ($kategori_lama) {
                        $kategori_lama->saldo = $kategori_lama->saldo + $transaksi->jumlah;
                        $kategori_lama->save();
                    }
                } else {
                    $kategori_balik = Category::where('user_id', $user_id)->where('warna', 'success')->orderBy('id', 'desc')->first();
                    if ($kategori_balik) {
                        $kategori_balik->saldo = $kategori_balik->saldo - $transaksi->jumlah;
                        $kategori_balik->save();
                    }
                }

                return redirect()->back()->withInput()->with('error', 'Saldo pemasukan tidak cukup!');
            }

            // Potong saldo di kategori pemasukan buat yg baru
            $this->potongSaldoKategoriPemasukan($user_id, $request->jumlah);
        }

        // Terapkan perubahan kategori baru kalau pemasukan
        if ($request->tipe == 'pemasukan') {
            $kategori_baru = Category::find($request->category_id);
            if ($kategori_baru) {
                $kategori_baru->saldo = $kategori_baru->saldo + $request->jumlah;
                $kategori_baru->save();
            }
        }

        $transaksi->judul = $request->judul;
        $transaksi->tipe = $request->tipe;
        $transaksi->category_id = $request->category_id;
        $transaksi->jumlah = $request->jumlah;
        $transaksi->tanggal = $request->tanggal;
        $transaksi->keterangan = $request->keterangan;
        $transaksi->save();

        if ($notif_pengeluaran != null) {
            return redirect()->route('transaksi.index')
                ->with('success', 'Transaksi berhasil diupdate.')
                ->with('warning', $notif_pengeluaran);
        }

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diupdate.');
    }

    public function destroy($id)
    {
        $transaksi = Transaction::find($id);

        if ($transaksi->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        // Kalau dihapus berarti batalin pemasukan
        if ($transaksi->tipe == 'pemasukan') {
            $kategori = Category::find($transaksi->category_id);
            if ($kategori) {
                $kategori->saldo = $kategori->saldo - $transaksi->jumlah;
                $kategori->save();
            }
        } else {
            // Kalau dihapus batalin pengeluaran, balikin saldonya
            $kategori_balik = Category::where('user_id', Auth::user()->id)->where('warna', 'success')->orderBy('id', 'desc')->first();
            if ($kategori_balik) {
                $kategori_balik->saldo = $kategori_balik->saldo + $transaksi->jumlah;
                $kategori_balik->save();
            }
        }

        $transaksi->delete();

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function uploadStruk(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp',
        ]);

        $userId = Auth::user()->id;
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            $message = 'GEMINI_API_KEY belum di-set.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 500);
            }
            return redirect()->route('transaksi.index')->with('error', $message);
        }

        $image = $request->file('image');
        $imageData = base64_encode(file_get_contents($image->getRealPath()));

        $prompt = 'Dari gambar struk/bukti pembayaran ini (bisa struk kasir atau struk digital seperti DANA/OVO/GoPay), ambil nominal yang BENAR-BENAR DIBAYAR (keyword umum: TOTAL, GRAND TOTAL, TOTAL BAYAR, AMOUNT PAID). Abaikan subtotal, pajak, saldo, cashback, dan angka item.
    Jawab HANYA dalam format JSON persis seperti ini: {"total": 50000}. Angka harus tanpa simbol mata uang, tanpa titik/koma pemisah, tanpa teks lain.';

        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $image->getMimeType(),
                                'data' => $imageData,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        try {
            [$response, $usedVersion, $usedModel] = $this->geminiGenerateContentWithFallbacks($apiKey, $model, $payload);
        } catch (ConnectionException $e) {
            $message = 'Koneksi ke Gemini gagal: ' . $e->getMessage();
            logger()->error('Gemini connection error', ['error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 502);
            }
            return redirect()->route('transaksi.index')->with('error', $message);
        }

        if (!$response->successful()) {
            $result = $response->json();
            $providerMessage = data_get($result, 'error.message');
            $status = $response->status();

            $message = 'Gagal memproses struk via Gemini.';
            if ($providerMessage) {
                $message .= " ({$status}) {$providerMessage}";
            } else {
                $message .= " (HTTP {$status})";
            }

            if (isset($usedVersion, $usedModel)) {
                $message .= " | Model: {$usedModel} | API: {$usedVersion}";
            }

            if (config('app.debug')) {
                $body = trim($response->body());
                if ($body !== '') {
                    $message .= ' | Body: ' . mb_strimwidth($body, 0, 500, '...');
                }
            }

            logger()->error('Gemini non-success response', [
                'status' => $status,
                'body' => $response->body(),
                'used_version' => $usedVersion ?? null,
                'used_model' => $usedModel ?? null,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ], 502);
            }
            return redirect()->route('transaksi.index')->with('error', $message);
        }

        $result = $response->json();
        $text = data_get($result, 'candidates.0.content.parts.0.text', '');
        $total = $this->parseTotalFromGeminiText($text);

        if ($total < 1 || $total > 999999999999) {
            $message = 'Total dari struk tidak valid.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'raw' => $text,
                ], 422);
            }
            return redirect()->route('transaksi.index')->with('error', $message);
        }

        $kategoriStruk = Category::firstOrCreate(
            ['user_id' => $userId, 'nama_kategori' => 'Struk'],
            ['ikon' => 'bi-receipt', 'warna' => 'danger', 'saldo' => 0]
        );

        // Cek saldo & potong saldo kategori pemasukan (konsisten dengan store())
        $totalMasuk = Transaction::where('user_id', $userId)->where('tipe', 'pemasukan')->sum('jumlah');
        $totalKeluar = Transaction::where('user_id', $userId)->where('tipe', 'pengeluaran')->sum('jumlah');
        $sisaSaldo = $totalMasuk - $totalKeluar;

        if ($sisaSaldo < $total) {
            $message = 'Saldo pemasukan tidak cukup untuk melakukan pengeluaran!';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'total' => $total,
                    'sisa_saldo' => $sisaSaldo,
                ], 422);
            }
            return redirect()->route('transaksi.index')->with('error', $message);
        }

        $notifPengeluaran = null;
        $tanggal = Carbon::now();
        $awalBulan = $tanggal->copy()->startOfMonth()->toDateString();
        $akhirBulan = $tanggal->copy()->endOfMonth()->toDateString();
        $totalKeluarBulanIni = Transaction::where('user_id', $userId)
            ->where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->sum('jumlah');

        $kelipatanLama = floor($totalKeluarBulanIni / 1000000);
        $kelipatanBaru = floor(($totalKeluarBulanIni + $total) / 1000000);
        if ($kelipatanBaru > $kelipatanLama && $kelipatanBaru > 0) {
            $rupiah = number_format($kelipatanBaru * 1000000, 0, ',', '.');
            $notifPengeluaran = "Peringatan: Total pengeluaran Anda bulan ini telah mencapai Rp {$rupiah}. Harap perhatikan keuangan Anda!";
        }

        $this->potongSaldoKategoriPemasukan($userId, $total);

        $transaksi = new Transaction();
        $transaksi->user_id = $userId;
        $transaksi->judul = 'Struk';
        $transaksi->tipe = 'pengeluaran';
        $transaksi->category_id = $kategoriStruk->id;
        $transaksi->jumlah = $total;
        $transaksi->tanggal = Carbon::now()->toDateString();
        $transaksi->keterangan = 'Dibuat otomatis dari upload struk (Gemini)';
        $transaksi->save();

        if ($request->expectsJson()) {
            return response()->json([
                'total' => $total,
                'transaction_id' => $transaksi->id,
                'warning' => $notifPengeluaran,
            ], 201);
        }

        $redirect = redirect()->route('transaksi.index')->with('success', 'Struk berhasil diproses. Transaksi pengeluaran otomatis ditambahkan.');
        if ($notifPengeluaran != null) {
            $redirect->with('warning', $notifPengeluaran);
        }
        return $redirect;
    }

    private function parseTotalFromGeminiText(string $text): int
    {
        $text = trim($text);

        // Strip code fences if Gemini returns ```json ... ```
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);

        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $candidate = $decoded['total'] ?? null;
            if (is_int($candidate)) {
                return $candidate;
            }
            if (is_string($candidate)) {
                $digits = preg_replace('/\D/', '', $candidate);
                return $digits === '' ? 0 : (int) $digits;
            }
        }

        // Fallback: grab the first number-like sequence and strip non-digits
        if (preg_match('/(\d[\d\s\.,]*)/', $text, $m)) {
            $digits = preg_replace('/\D/', '', $m[1]);
            return $digits === '' ? 0 : (int) $digits;
        }

        return 0;
    }

    /**
     * @return array{0:\Illuminate\Http\Client\Response,1:string,2:string} [response, apiVersion, model]
     */
    private function geminiGenerateContentWithFallbacks(string $apiKey, string $model, array $payload): array
    {
        $model = $this->normalizeGeminiModelName($model);

        // Try both API versions with the configured model
        foreach (['v1beta', 'v1'] as $version) {
            $response = $this->geminiPostGenerateContent($apiKey, $version, $model, $payload);
            if ($response->successful()) {
                return [$response, $version, $model];
            }

            // If it's a transient error, keep going to fallbacks.
            if (in_array($response->status(), [429, 500, 502, 503, 504], true)) {
                continue;
            }

            // If it's not a 404, don't keep switching versions blindly
            if ($response->status() !== 404) {
                return [$response, $version, $model];
            }
        }

        // Auto-discover usable flash models for this key, then retry in order
        foreach (['v1beta', 'v1'] as $version) {
            $discoveredList = $this->discoverGeminiFlashModels($apiKey, $version);
            foreach ($discoveredList as $discovered) {
                // Skip if same as already tried
                if ($discovered === $model) {
                    continue;
                }

                $response = $this->geminiPostGenerateContent($apiKey, $version, $discovered, $payload);
                if ($response->successful()) {
                    return [$response, $version, $discovered];
                }

                // Non-transient hard failures: stop early and report
                if (!in_array($response->status(), [404, 429, 500, 502, 503, 504], true)) {
                    return [$response, $version, $discovered];
                }
            }
        }

        // Worst case: return the last attempt (v1) for error reporting
        $response = $this->geminiPostGenerateContent($apiKey, 'v1', $model, $payload);
        return [$response, 'v1', $model];
    }

    private function geminiPostGenerateContent(string $apiKey, string $version, string $model, array $payload)
    {
        $model = $this->normalizeGeminiModelName($model);
        $endpoint = "https://generativelanguage.googleapis.com/{$version}/models/{$model}:generateContent?key={$apiKey}";

        return Http::timeout(60)
            ->acceptJson()
            ->retry(
                3,
                400,
                function ($exception, $pendingRequest, $method) {
                    if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                        return in_array($exception->response->status(), [429, 500, 502, 503, 504], true);
                    }

                    return $exception instanceof ConnectionException;
                },
                false
            )
            ->post($endpoint, $payload);
    }

    private function normalizeGeminiModelName(string $model): string
    {
        $model = trim($model);
        if (str_starts_with($model, 'models/')) {
            $model = substr($model, 7);
        }
        return $model;
    }

    /**
     * @return string[]
     */
    private function discoverGeminiFlashModels(string $apiKey, string $version): array
    {
        $endpoint = "https://generativelanguage.googleapis.com/{$version}/models?key={$apiKey}";

        $response = Http::timeout(30)->acceptJson()->get($endpoint);
        if (!$response->successful()) {
            return [];
        }

        $models = (array) data_get($response->json(), 'models', []);
        $candidates = [];

        foreach ($models as $m) {
            $name = (string) ($m['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $short = $this->normalizeGeminiModelName($name);

            $methods = (array) ($m['supportedGenerationMethods'] ?? []);
            if (!in_array('generateContent', $methods, true)) {
                continue;
            }

            $candidates[] = $short;
        }

        if (!$candidates) {
            return [];
        }

        // Prefer 1.5 Flash, then 2.0 Flash, then 2.5 Flash, then any Flash
        $ordered = [];
        $preferPrefixes = [
            'gemini-1.5-flash',
            'gemini-2.0-flash',
            'gemini-2.5-flash',
        ];

        foreach ($preferPrefixes as $prefix) {
            foreach ($candidates as $cand) {
                if ($cand === $prefix || str_starts_with($cand, $prefix . '-')) {
                    $ordered[] = $cand;
                }
            }
        }

        foreach ($candidates as $cand) {
            if (str_contains($cand, 'flash') && !in_array($cand, $ordered, true)) {
                $ordered[] = $cand;
            }
        }

        // Fallback to whatever is available
        foreach ($candidates as $cand) {
            if (!in_array($cand, $ordered, true)) {
                $ordered[] = $cand;
            }
        }

        return $ordered;
    }

    private function potongSaldoKategoriPemasukan($user_id, $sisa_potong)
    {
        $kategori_pemasukan = Category::where('user_id', $user_id)->where('warna', 'success')->where('saldo', '>', 0)->orderBy('saldo', 'desc')->get();
        foreach ($kategori_pemasukan as $kat) {
            if ($sisa_potong <= 0) break;

            if ($kat->saldo >= $sisa_potong) {
                $kat->saldo = $kat->saldo - $sisa_potong;
                $sisa_potong = 0;
            } else {
                $sisa_potong = $sisa_potong - $kat->saldo;
                $kat->saldo = 0;
            }
            $kat->save();
        }
    }
}
