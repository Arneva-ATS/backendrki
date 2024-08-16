<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PengajuanController extends Controller
{
    public function store(Request $request)
    {
        $messageErrors = [
            'nama_koperasi.required'        => 'Nama koperasi harus diisi.',
            'nama_koperasi.string'          => 'Nama koperasi harus berupa teks.',
            'nama_koperasi.max'             => 'Nama koperasi maksimal 100 karakter.',
            'username.required'             => 'Username koperasi harus diisi.',
            'password.required'             => 'Password harus diisi.',
            'confirmPassword.required'      => 'Konfirmasi Password harus diisi.',
            'nama_ketua.required'           => 'Nama ketua harus diisi.',
            'nama_ketua.string'             => 'Nama ketua harus berupa teks.',
            'nama_ketua.max'                => 'Nama ketua maksimal 100 karakter.',
            'nomer_ketua.required'          => 'Nomer ketua harus diisi.',
            'nomer_ketua.string'            => 'Nomer ketua harus berupa teks.',
            'nomer_ketua.regex'             => 'Format nomer ketua tidak valid.',
            'tingkatan_koperasi.required'   => 'Tingkatan koperasi harus diisi.',
            'tingkatan_koperasi.integer'     => 'Tingkatan koperasi harus berupa integer.',
            'tingkatan_koperasi.in'         => 'Tingkatan koperasi tidak valid.',
        ];


        $validator = Validator::make($request->all(), [
            'nama_koperasi'      => 'required|string|max:100',
            'username'           => 'required|string|max:100',
            'password'           => 'required|string|max:100',
            'confirmPassword'           => 'required|string|max:100',
            'nama_ketua'         => 'required|string|max:100',
            'nomer_ketua'        => ['required', 'string', 'regex:/^(08|\+628)[0-9]{8,11}$/'],
            'tingkatan_koperasi' => 'required|integer|in:1,2,3',
        ], $messageErrors);

        // Validation Check
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        if ($request->password != $request->confirmPassword) {
            return response()->json(['errors' => 'Password tidak sama!'], 400);
        }
        // Generate 4-digit OTP
        $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        DB::beginTransaction();
        try {
            $koperasiId = DB::table('tbl_koperasi')->insertGetId([
                'nama_koperasi'      => $request->nama_koperasi,
                'username'           => $request->username,
                'password'           => $request->password,
                'slug'                 => $request->slug,
                'id_tingkatan_koperasi'   => $request->tingkatan_koperasi,
            ]);
            if (!$koperasiId) {
                throw new \Exception('Gagal menyimpan koperasi!');
            }

            DB::table('tbl_pengurus')->insert([
                'id_koperasi' => $koperasiId,
                'nama_pengurus'   => $request->nama_ketua,
                'nomor_hp'        => $request->nomer_ketua,
                'jabatan' => 'ketua'
            ]);

            DB::table('tbl_otp')->insert([
                'id_koperasi' => $koperasiId,
                'otp_code' => $otp
            ]);
            DB::commit();

            return response()->json(['message' => [
                'nama_koperasi'      => $request->nama_koperasi,
                'username'           => $request->username,
                'password'           => $request->password,
                'slug'                 => $request->slug,
                'id_tingkatan_koperasi'   => $request->tingkatan_koperasi,
            ]], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan pengajuan', 'details' => $e->getMessage()], 500);
        }
    }

    public function otp(Request $request)
    {
        $messageErrors = [
            'otp.required'  => 'OTP harus diisi.',
        ];


        $validator = Validator::make($request->all(), [
            'otp'      => 'required|string|max:4',
        ], $messageErrors);

        // Validation Check
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $otp = $request->otp;
        // Generate 3-digit NIS
        $nis = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        try {
            $verify_otp = DB::table('tbl_otp')->where('otp_code', $otp)->first();
            if (!$verify_otp) {
                throw new \Exception('OTP Salah!');
            }
            DB::table('tbl_koperasi')->where('id', $verify_otp->id_koperasi)->update([
                'nis' => $nis
            ]);

            $get_nis = DB::table('tbl_koperasi')->where('nis', $nis)->first();
            if (!$get_nis) {
                throw new \Exception('Gagal mendapatkan NIS!');
            }
            $delete_otp = DB::table('tbl_otp')->where('otp_code', $otp)->delete();
            if(!$delete_otp){
                throw new \Exception('Gagal Delete OTP!');
            }
            return response()->json(['message' => 'Berhasil verifikasi OTP', 'details'=>$get_nis], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat verifikasi OTP', 'details' => $e->getMessage()], 500);
        }
    }
}
