<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsUpDelKoperasiController extends Controller
{
    //
    public function run(Request $request)
    {
        $cmd = $request->get("cmd");
        if ($cmd == "InsTrSimpan") {
            $result = $this->InsTrSimpan($request, $cmd);
        } else if ($cmd == "InsTrPengajuanPinjaman") {
            $result = $this->InsTrPengajuanPinjaman($request, $cmd);
        } else if ($cmd == "updAppTrSimpan") {
            $result = $this->updAppTrSimpan($request, $cmd);
        } else if ($cmd == "updVerifyTrSimpan") {
            $result = $this->updVerifyTrSimpan($request, $cmd);
        } else if ($cmd == "insPengajuan") {
            $result = $this->insPengajuan($request, $cmd);
        } else if ($cmd == "updPengajuan") {
            $result = $this->updPengajuan($request, $cmd);
        } else if ($cmd == "updAngsuran") {
            $result = $this->updAngsuran($request, $cmd);
        } else if ($cmd == "appSimpanan") {
            $result = $this->appSimpanan($request, $cmd);
        } else if ($cmd == "InsTrKas") {
            $result = $this->InsTrKas($request, $cmd);
        } else if ($cmd == "appTran") {
            $result = $this->appTran($request, $cmd);
        } else {
            $result = $this->noCmd($request, $cmd);
        }
        return $result;
    }

    public function noCmd(Request $request, $cmd)
    {
        $resutlMsg = array("sts" => "Error", "desc" => "No Command For This API", "msg" => "No Command For This API");
        return json_encode($resutlMsg);
    }

    public function InsTrSimpan(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kode_transaksi = $_POST["kode_transaksi"];
        $tgl_transaksi = $_POST["tgl_transaksi"];
        $t2_jenis_simpanan = $_POST["t2_jenis_simpanan"];
        $coa_2nd = $_POST["coa_2nd"];
        $amount = $_POST["amount"];
        $noAnggota = $_POST["noAnggota"];
        $coa_3rd = $_POST["coa_3rd"];
        $no_pengajuan = $_POST["no_pengajuan"];
        $pktr = $_POST["pktr"];

        $sql = "select * from tr_simpan where noSimpan='" . $kode_transaksi . "'  ";

        $resutlMax = DB::select($sql);
        if (count($resutlMax) > 0) {
            $resutlMsg = array("sts" => "N", "desc" => "Nomor Transaksi Sudah Ada !");
        } else {

            $sql = "insert into tr_simpan values ('" . $kode_transaksi . "','" . $pktr . "','" . $tgl_transaksi . "','" . $coa_2nd . "','" . $coa_3rd . "','" . $noAnggota . "','" . $t2_jenis_simpanan . "','" . $amount . "','UNAPPROVE','UNVERIFY',now(),'',now(),'','" . $userIns . "',now() ) ";
            $resultIns = DB::insert($sql);

            if ($resultIns > 0) {
                $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resutlMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        }
        return json_encode($resutlMsg);
    }

    public function InsTrKas(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kode_transaksi = $_POST["kode_transaksi"];
        $tgl_transaksi = $_POST["tgl_transaksi"];
        $jenis_transaksi = $_POST["jenis_transaksi"];
        $coa_2nd = $_POST["coa_2nd"];
        $amount = $_POST["amount"];
        $kop_id = $_POST["kop_id"];
        $nama_transaksi = $_POST["nama_transaksi"];
        $pktr = $_POST["pktr"];
        $noTranContra = $_POST["noTranContra"];

        $sql = "select * from tr_kas where notran='" . $kode_transaksi . "'  ";

        $resutlMax = DB::select($sql);
        if (count($resutlMax) > 0) {
            $resutlMsg = array("sts" => "N", "desc" => "Nomor Transaksi Sudah Ada !");
        } else {

            $sql = "insert into tr_kas values ('" . $kode_transaksi . "','" . $pktr . "','" . $noTranContra . "','" . $tgl_transaksi . "','" . $coa_2nd . "','" . $kop_id . "','" . $nama_transaksi . "','" . $jenis_transaksi . "','" . $amount . "','UNAPPROVE','UNVERIFY',now(),'',now(),'','" . $userIns . "',now() ) ";
            $resultIns = DB::insert($sql);

            if ($resultIns > 0) {
                $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resutlMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        }
        return json_encode($resutlMsg);
    }

    public function InsTrPengajuanPinjaman(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kop_id = $_POST["kop_id"];
        $kode_transaksi = $_POST["kode_transaksi"];
        //$tgl_transaksi = $_POST["tgl_transaksi"];
        $tipe_pinjaman = $_POST["tipe_pinjaman"];
        $keterangan = $_POST["keterangan"];
        $noAnggota = $_POST["noAnggota"];
        $jumlah_pinjaman = $_POST["jumlah_pinjaman"];
        $jumlah_angsuran = $_POST["jumlah_angsuran"];
        $lama_pinjaman = $_POST["lama_pinjaman"];
        $lama_pinjaman_bulan = $_POST["lama_pinjaman_bulan"];
        $status_pinjaman = $_POST["status_pinjaman"];
        $no_pengajuan = $_POST["no_pengajuan"];
        $pktr = $_POST["pktr"];

        $sql = " SELECT *,concat(DATE_FORMAT(NOW(), '%Y%m%d-%H%i%s'),'" . $no_pengajuan . "') nomor FROM mst_anggota where no_anggota='" . $noAnggota . "'  ";
        $resutlMax = DB::select($sql);


        if (count($resutlMax) > 0) {
            $sql = "insert into tr_pengajuan_pinjaman values ('" . $resutlMax[0]->nomor . "','" . $pktr . "','" . $kop_id . "','" . $noAnggota . "',now(),'" . $tipe_pinjaman . "','" . $jumlah_pinjaman . "'," . $jumlah_angsuran . ",
      '" . $lama_pinjaman . "','" . $lama_pinjaman_bulan . "','" . $keterangan . "','" . $status_pinjaman . "',now(),'',null,'" . $userIns . "',null,null ) ";
            $resultIns = DB::insert($sql);
            $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");

            if ($resultIns > 0) {
                $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resutlMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "User Id Tidak Ada Harap Daftar Dahulu");
        }
        return json_encode($resutlMsg);
    }

    public function updAppTrSimpan(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $noTrans = $_POST["noTrans"];

        $sql = "select * from tr_simpan where noSimpan='" . $noTrans . "'  ";
        $resutlMax = DB::select($sql);
        if (count($resutlMax) > 0) {
            $sql = "update tr_simpan set appyn='APPROVE' ,appDt=now(),appUser='" . $userIns . "' where noSimpan='" . $noTrans . "' ";
            $resultIns = DB::update($sql);

            if ($resultIns > 0) {
                $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resutlMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "Tidak Ada Nomor Transaksi !");
        }
        return json_encode($resutlMsg);
    }

    public function updVerifyTrSimpan(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $noTrans = $_POST["noTrans"];

        $sql = "select * from tr_simpan where noSimpan='" . $noTrans . "'  ";
        $resutlMax = DB::select($sql);
        if (count($resutlMax) > 0) {
            $sql = "update tr_simpan set verify='VERIFY' ,verifyDt=now(),verifyUser='" . $userIns . "' where noSimpan='" . $noTrans . "' ";
            $resultIns = DB::update($sql);

            if ($resultIns > 0) {
                $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resutlMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "Tidak Ada Nomor Transaksi !");
        }
        return json_encode($resutlMsg);
    }


    public function updAngsuran(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $nomor_pinjaman = $_POST["nomor_pinjaman"];
        $nomor_pengajuan = $_POST["nomor_pengajuan"];

        $sql = "select * from tr_angsuran where nomor_pinjaman='" . $nomor_pinjaman . "' and no_pengajuan_idx='" . $nomor_pengajuan . "'  ";
        //var_dump($sql);
        $resutlMax = DB::select($sql);
        if (count($resutlMax) > 0) {
            $sql = "update tr_angsuran set sts='LUNAS' ,updDt=now(),updUser='" . $userIns . "' where nomor_pinjaman='" . $nomor_pinjaman . "' and no_pengajuan_idx='" . $nomor_pengajuan . "' ";
            $resultIns = DB::update($sql);
            $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "Tidak Ada Nomor Transaksi !");
        }
        return json_encode($resutlMsg);
    }
    public function insPengajuan(Request $request, $cmd)
    {
        $no_pengajuan = $_POST["no_pengajuan"];
        $tanggal_pengajuan = $_POST["tanggal_pengajuan"];
        $no_anggota = $_POST["no_anggota"];
        $kop_id = $_POST["kop_id"];
        $tipe_pinjaman = $_POST["tipe_pinjaman"];
        $jumlah_pinjaman = $_POST["jumlah_pinjaman"];
        $jumlah_angsuran = $_POST["jumlah_angsuran"];
        $lama_pinjaman = $_POST["lama_pinjaman"];
        $lama_pinjaman_bulan = $_POST["lama_pinjaman_bulan"];
        $status = $_POST["status"];
        $status_dt = $_POST["status_dt"];
        $keterangan = $_POST["keterangan"];
        $pktr = $_POST["pktr"];
        $ins_user = $_POST["userId"];

        $loop = $lama_pinjaman_bulan;

        $sql = " SELECT *,concat(DATE_FORMAT(NOW(), '%Y%m%d-%H%i%s'),'" . $no_pengajuan . "') nomor FROM mst_anggota where no_anggota='" . $no_anggota . "'  ";
        $resutlMax = DB::select($sql);

        if (count($resutlMax) > 0) {

            $sql = "SELECT * FROM tr_angsuran WHERE no_anggota='" . $no_anggota . "' AND sts='UTANG'   ";
            $resutlPengajuan = DB::select($sql);
            if (count($resutlPengajuan) > 0) {
                $resutlMsg = array("sts" => "N", "desc" => "Anggota Ini Masih Punya Angsuran Yang Belum Lunas \n Harap Lunasi dulu Transaksi Anda ", "msg" => "");
            } else {
                $sql = "insert into tr_pengajuan_pinjaman values ('" . $resutlMax[0]->nomor . "','" . $pktr . "','" . $kop_id . "','" . $no_anggota . "',now(),'" . $tipe_pinjaman . "','" . $jumlah_pinjaman . "'," . $jumlah_angsuran . ",
        '" . $lama_pinjaman . "','" . $lama_pinjaman_bulan . "','" . $keterangan . "','" . $status . "',now(),'',null,'" . $ins_user . "',null,null ) ";
                DB::insert($sql);
                $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            }
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "User Id Tidak Ada Harap Daftar Dahulu");
        }

        return json_encode($resutlMsg);
    }

    public function updPengajuan(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $noTrans = $_POST["noTrans"];
        $sts = $_POST["sts"];

        $sql = " select * from tr_pengajuan_pinjaman where noPengajuan='" . $noTrans . "'  ";
        $resutlMax = DB::select($sql);
        $loop = $resutlMax[0]->lama_pinjaman_bulan;
        if (count($resutlMax) > 0) {
            $sql = "update tr_pengajuan_pinjaman set tgl_cair=now() ,upd_Dt=now(),status='" . $sts . "' where noPengajuan='" . $noTrans . "' ";
            $resultIns = DB::update($sql);
            if ($sts == "000003") {

                for ($x = 1; $x <= $loop; $x++) {
                    $sql = " insert into tr_angsuran VALUE (concat('ANG','" . $x . "'),'" . $resutlMax[0]->noPengajuan . "','" . $resutlMax[0]->idx . "','" . $resutlMax[0]->no_anggota . "','" . $resutlMax[0]->kop_id . "',NOW()," . $resutlMax[0]->jumlah_angsuran . "," . $x . ",0,'','','UTANG','" . $userIns . "',NOW(),'',null) ";
                    $result = DB::insert($sql);
                    if ($resultIns == "OK") {
                    } else {
                        $resutlMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
                    }
                }
                $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {

                $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            }
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "Tidak Ada Nomor Transaksi !");
        }
        return json_encode($resutlMsg);
    }

    public function appSimpanan(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $noTrans = $_POST["noTrans"];
        //$kop_id= $_POST["kop_id"];
        $sts = $_POST["sts"];

        $sql = "SELECT * FROM tr_simpan WHERE noSimpan='" . $noTrans . "'  ";
        //var_dump($sql);
        $resutlMax = DB::select($sql);
        if (count($resutlMax) > 0) {
            $sql = "update tr_simpan set appYn='" . $sts . "',appDt=now(),appUser='" . $userIns . "' where noSimpan='" . $noTrans . "' ";
            $resultIns = DB::update($sql);
            $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "Tidak Ada Nomor Transaksi !");
        }
        return json_encode($resutlMsg);
    }

    public function appTran(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $noTrans = $_POST["noTrans"];
        $sts = $_POST["sts"];

        $sql = "SELECT * FROM tr_kas WHERE noTran='" . $noTrans . "'  ";
        //var_dump($sql);
        $resutlMax = DB::select($sql);
        if (count($resutlMax) > 0) {
            $sql = "update tr_kas set appYn='" . $sts . "',appDt=now(),appUser='" . $userIns . "' where noTran='" . $noTrans . "' ";
            $resultIns = DB::update($sql);
            $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "Tidak Ada Nomor Transaksi !");
        }
        return json_encode($resutlMsg);
    }
}
