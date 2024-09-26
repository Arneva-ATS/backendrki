<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsUpDelMasterController extends Controller
{
    //
    public function run(Request $request)
    {
        $cmd = $request->get("cmd");
        if ($cmd == "delUser") {
            $result = $this->delUser($request, $cmd);
        } else if ($cmd == "delCodeDetail") {
            $result = $this->delCodeDetail($request, $cmd);
        } else if ($cmd == "InsRegKoperasi") {
            $result = $this->InsRegKoperasi($request, $cmd);
        } else if ($cmd == "InsUser") {
            $result = $this->InsUser($request, $cmd);
        } else if ($cmd == "InsBarang") {
            $result = $this->InsBarang($request, $cmd);
        } else if ($cmd == "insGroupCoa") {
            $result = $this->insGroupCoa($request, $cmd);
        } else if ($cmd == "insGroupCoaL2") {
            $result = $this->insGroupCoaL2($request, $cmd);
        } else if ($cmd == "insGroupCoaL3") {
            $result = $this->insGroupCoaL3($request, $cmd);
        } else if ($cmd == "InsCodeDetail") {
            $result = $this->InsCodeDetail($request, $cmd);
        } else if ($cmd == "insTempAnggota") {
            $result = $this->insTempAnggota($request, $cmd);
        } else if ($cmd == "insAnggota") {
            $result = $this->insAnggota($request, $cmd);
        } else if ($cmd == "appDaftarAnggota") {
            $result = $this->appDaftarAnggota($request, $cmd);
        } else if ($cmd == "delBarang") {
            $result = $this->delBarang($request, $cmd);
        } else if ($cmd == "delGudang") {
            $result = $this->delGudang($request, $cmd);
        } else if ($cmd == "InsGudang") {
            $result = $this->InsGudang($request, $cmd);
        } else if ($cmd == "delPembeli") {
            $result = $this->delPembeli($request, $cmd);
        } else if ($cmd == "InsPembeli") {
            $result = $this->InsPembeli($request, $cmd);
        } else if ($cmd == "delSuplier") {
            $result = $this->delSuplier($request, $cmd);
        } else if ($cmd == "InsSuplier") {
            $result = $this->InsSuplier($request, $cmd);
        } else if ($cmd == "delAnggota") {
            $result = $this->delAnggota($request, $cmd);
        } else if ($cmd == "delTempAnggota") {
            $result = $this->delTempAnggota($request, $cmd);
        } else if ($cmd == "insKoperasi") {
            $result = $this->insKoperasi($request, $cmd);
        } else if ($cmd == "delKoperasi") {
            $result = $this->delKoperasi($request, $cmd);
        } else if ($cmd == "insToko") {
            $result = $this->insToko($request, $cmd);
        } else if ($cmd == "delToko") {
            $result = $this->delToko($request, $cmd);
        } else if ($cmd == "updHakuser") {
            $result = $this->updHakuser($request, $cmd);
        } else if ($cmd == "delBarangRutin") {
            $result = $this->delBarangRutin($request, $cmd);
        } else if ($cmd == "insBarangRutin") {
            $result = $this->insBarangRutin($request, $cmd);
        } else if ($cmd == "delPriceFello") {
            $result = $this->delPriceFello($request, $cmd);
        } else if ($cmd == "InsFelloPrice") {
            $result = $this->InsFelloPrice($request, $cmd);
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


    public function updHakUser()
    {
        $userId = $_POST["userId"];
        $arrData = $_POST["arrData"];
        $resultUpd = 0;
        //var_dump($arrData);
        if (count($arrData) > 0) {
            for ($x = 0; $x < count($arrData); $x++) {
                $menuCode = $arrData[$x]["menuCode"];
                $showYn = $arrData[$x]["showYn"];
                $saveYn = $arrData[$x]["saveYn"];
                $editYn = $arrData[$x]["editYn"];
                $inqYn = $arrData[$x]["inqYn"];
                $deleteYn = $arrData[$x]["deleteYn"];
                $printYn = $arrData[$x]["printYn"];

                $sql = " SELECT * FROM user_sysmenu WHERE  userid='" . $userId . "' AND menuId='" . $menuCode . "' ";
                $results = DB::select($sql);

                if (count($results) > 0) {
                    $sql = "
          update  user_sysmenu set showYn='" . $showYn . "',saveYn='" . $saveYn . "',editYn='" . $editYn . "',inqYn='" . $inqYn . "',deleteYn='" . $deleteYn . "',printYn='" . $printYn . "'
          where userid='" . $userId . "' and menuId='" . $menuCode . "'
          ";
                    DB::update($sql);
                    $resultUpd = $resultUpd + 1;
                } else {
                    $sql = "
          insert into  user_sysmenu (showYn,saveYn,editYn,inqYn,deleteYn,printYn,userid,menuId) values ('" . $showYn . "','" . $saveYn . "','" . $editYn . "',
          '" . $inqYn . "','" . $deleteYn . "','" . $printYn . "','" . $userId . "','" . $menuCode . "' ) ";
                    DB::insert($sql);
                    $resultUpd = $resultUpd + 1;
                }
            }
        }

        if ($resultUpd > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultUpd);
        }
        return  json_encode($resutlMsg);
    }

    public function delUser(Request $request, $cmd)
    {
        $userId = $_POST["userId"];
        $sql = " DELETE FROM  mst_user  WHERE userid=?  ";
        $results = DB::delete($sql, [$userId]);

        if ($results > 0) {
            $sql = " DELETE FROM  user_sysmenu  WHERE userid=?  ";
            DB::delete($sql, [$userId]);
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }

    public function delCodeDetail(Request $request, $cmd)
    {
        $masterCode = $_POST["masterCode"];
        $detailCode = $_POST["detailCode"];
        $detailKop = $_POST["detailKop"];

        $sql = "
        DELETE FROM  mst_code_detail  WHERE masterCode=?
        and detailCode=? and detailKop=?
         ";
        $results = DB::delete($sql, [$masterCode, $detailCode, $detailKop]);
        if ($results > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }

    public function InsUser(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kop_id = $_POST["kop_id"];
        $userId = $_POST["userId"];
        $userNm = $_POST["userNm"];
        $namaUser = $_POST["namaUser"];
        $userPwd = $_POST["userPwd"];
        $aktif = $_POST["aktif"];
        $tipeUser = $_POST["tipeUser"];
        $toko_id = $_POST["toko_id"];

        $sql = "select * from mst_user where userId='" . $userId . "'  ";
        $resultMax = DB::select($sql);
        if (count($resultMax) > 0) {
            //$resultMsg = array("sts" => "N", "desc" => " ", "msg" => " Tidak Dapat Update User Id, Silahkan Hubungi ADMINISTRATOR");
            $sql = "  update mst_user set  userNm='" . $userNm . "',userPwd='" . $userPwd . "',
                  no_koperasi='" . $kop_id . "',aktif='" . $aktif . "',level='" . $tipeUser . "',namaUser='" . $namaUser . "',no_toko='" . $toko_id . "',
                  userIns='" . $userIns . "',dateins=now()
                  where userId='" . $userId . "'  ";
            DB::insert($sql);
            $resultMsg = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
        } else {
            $sql = "insert into mst_user values ('" . $userId . "','" . $userNm . "','" . $userPwd . "','" . $kop_id . "','" . $toko_id . "','" . $namaUser . "','Y','" . $tipeUser . "','" . $userIns . "',now() ) ";
            $resultIns = DB::insert($sql);
            if ($resultIns > 0) {
                $sql = "
        INSERT INTO user_sysmenu
        SELECT '" . $userId . "',
        menuCode,
        'N',
        'N',
        'N',
        'N',
        'N',
        'N',
        '" . $userIns . "',
        now()
         FROM sysmenu WHERE menuCode not IN ('ME001MO001','ME001MO008','ME001MO019')
        ";
                DB::insert($sql);

                $sql = "";
                if ($tipeUser == "ADMIN_ENTRY") {
                    $sql = "update user_sysmenu set showYn='Y' where userid='" . $userId . "' and menuId in ('M01','ME005','MT004','MT004MO001','MT004MO002','MT004MO004','MR004','MR004MO001','MR004MO002');";
                } else if ($tipeUser == "ADMIN_PENGURUS") {
                    $sql = "update user_sysmenu set showYn='Y' where userid='" . $userId . "' and menuId not in ('ME001MO008','ME001MO001','ME001MO019');";
                } else if ($tipeUser == "ADMIN_CHECKER") {
                    $sql = "update user_sysmenu set showYn='Y' where userid='" . $userId . "' and menuId in ('M01','ME005','MT004','MT004MO001','MT004MO002','MT004MO004','MT004MO003','MR004','MR004MO001','MR004MO002','MR004MO004','MR004MO005');";
                } else if ($tipeUser == "ADMIN_ACCOUNTING") {
                    $sql = "update user_sysmenu set showYn='Y' where userid='" . $userId . "' and menuId in ('M01','MR005','ME006','ME008','ME007','ME009','ME010','MR005MO001','MR005MO002','MR005MO003','MR005MO004','MR005MO005','MR005MO006','MR005MO007');";
                } else if ($tipeUser == "MANAGEMENT") {
                    $sql = "update user_sysmenu set showYn='Y' where userid='" . $userId . "' and menuId in ('ME006','ME008','ME007','ME009','ME010','ME006MO002','ME007MO002','ME008MO002','ME009MO002','ME010MO002');";
                }
                DB::update($sql);

                $sql = "select * from mst_code_detail where masterCode='1000022' and detailkop='" . $kop_id . "'  ";
                $resultPos = DB::select($sql);
                if (count($resultPos) == 0) {
                    $sql = " insert into mst_code_detail values ('1000022','000001','PPN','000002','1','Y','" . $kop_id . "','11','Y',now(),'" . $userIns . "' ) ";
                    DB::insert($sql);
                    $sql = " insert into mst_code_detail values ('1000022','000002','PRINTER','000001','2','Y','" . $kop_id . "','50','Y',now(),'" . $userIns . "' )  ";
                    DB::insert($sql);
                }

                $resultMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resultMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        }
        return json_encode($resultMsg);
    }

    public function InsBarang(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kop_id = $_POST["kop_id"];
        $toko_id = $_POST["toko_id"];
        $barcode_internal = $_POST["barcode_internal"];
        $barcode_kemasan = $_POST["barcode_kemasan"];
        $ppn = $_POST["ppn"];
        $minimum_stock = $_POST["minimum_stock"];
        $satuan = $_POST["satuan"];
        $jumlah_stock = $_POST["jumlah_stock"];
        $harga_jual = $_POST["harga_jual"];
        $harga_modal = $_POST["harga_modal"];
        $merk = $_POST["merk"];
        $kategory = $_POST["kategory"];
        $nama_barang = $_POST["nama_barang"];
        $kode_barang = $_POST["kode_barang"];
        $inventoryYn = $_POST["inventoryYn"];

        $sql = "select * from mst_barang where idx='" . $kode_barang . "'  ";
        $resultMax = DB::select($sql);

        if (count($resultMax) > 0) {
            $sql = "
        UPDATE  mst_barang SET
        nama_barang='" . $nama_barang . "',
        barcode_kemasan='" . $barcode_kemasan . "',
        barcode_internal='" . $barcode_internal . "',
        kategori='" . $kategory . "',
        unit='" . $satuan . "',
        merk='" . $merk . "',
        harga_modal='" . $harga_modal . "',
        harga_jual='" . $harga_jual . "',
        stock='" . $jumlah_stock . "',
        min_stock='" . $minimum_stock . "',
        ppn='" . $ppn . "',
        inventoryYn='" . $inventoryYn . "',
        insUser='" . $userIns . "',
        insDt=NOW()
        where idx='" . $kode_barang . "' ;

        ";

            $resultIns = DB::update($sql);
            $resultMsg = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
        } else {

            $sql = "
            insert into mst_barang values ('" . $kode_barang . "','" . $kop_id . "','" . $toko_id . "','" . $nama_barang . "','" . $barcode_kemasan . "','" . $barcode_internal . "',
            '" . $kategory . "','" . $satuan . "','" . $merk . "','" . $harga_modal . "','" . $harga_jual . "','" . $jumlah_stock . "',
            '" . $minimum_stock . "','" . $ppn . "','','" . $inventoryYn . "','" . $userIns . "',now() )
          ";

            $resultIns = DB::insert($sql);
            if ($resultIns == "OK") {
                $resultMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resultMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        }
        return json_encode($resultMsg);
    }

    public function insGroupCoa(Request $request, $cmd)
    {

        $userIns = $_POST["userIns"];
        $kop_id = $_POST["kop_id"];
        $code_coa = $_POST["code_coa"];
        $nama_group_coa = $_POST["nama_group_coa"];
        $coa_2nd = $_POST["coa_2nd"];
        $nama_koperasi = $_POST["nama_koperasi"];
        $coaIdx = $_POST["coaIdx"];
        $aktif = $_POST["aktif"];


        $sql = " SELECT * FROM mst_coa_group WHERE idx='" . $code_coa . "'  ";
        $resultMax = DB::select($sql);

        if (count($resultMax) > 0) {
            $resultMsg = array("sts" => "N", "desc" => " Id Sudah Ada ! Silahkan Gunakan Id Lain");
        } else {
            $sql = "insert into  mst_coa_group VALUE ('" . $code_coa . "','" . $kop_id . "',LEFT('" . $coa_2nd . "',2),'" . $coa_2nd . "','" . strtoupper($nama_group_coa) . "','','" . $coaIdx . "','" . $aktif . "','" . $userIns . "',NOW()); ";
            $resultIns = DB::insert($sql);

            if ($resultIns > 0) {
                $resultMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resultMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        }
        return json_encode($resultMsg);
    }

    public function insGroupCoaL2(Request $request, $cmd)
    {

        $userIns = $_POST["userIns"];
        $kop_id = $_POST["kop_id"];
        $code_coa = $_POST["code_coa"];
        $nama_group_coa = $_POST["nama_group_coa"];
        $nama_coa_2nd = $_POST["nama_coa_2nd"];
        $kode_coa_2nd = $_POST["kode_coa_2nd"];
        $idx_coa_2nd = $_POST["idx_coa_2nd"];
        $remark_coa_2nd = $_POST["remark_coa_2nd"];

        $sql = " SELECT * FROM mst_coa_l2 WHERE idx='" . $kode_coa_2nd . "'  ";
        $resultMax = DB::select($sql);

        if (count($resultMax) > 0) {
            $resultMsg = array("sts" => "N", "desc" => "Kode COA Sudah Ada ! Gunakan Kode Yang Lain ! ");
        } else {

            $sql = "INSERT INTO mst_coa_l2 VALUES ('" . $kode_coa_2nd . "','" . $code_coa . "','" . $kop_id . "','" . strtoupper($nama_coa_2nd) . "','" . $remark_coa_2nd . "','" . $idx_coa_2nd . "','Y','" . $userIns . "',NOW()); ";
            $resultIns = DB::insert($sql);

            if ($resultIns > 0) {
                $resultMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resultMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        }
        return json_encode($resultMsg);
    }

    public function insGroupCoaL3(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kop_id = $_POST["kop_id"];
        $code_coa = $_POST["code_coa"];
        $nama_group_coa = $_POST["nama_group_coa"];
        $kode_coa_2nd = $_POST["kode_coa_2nd"];

        $nama_coa_3rd = $_POST["nama_coa_3rd"];
        $kode_coa_3rd = $_POST["kode_coa_3rd"];
        $idx_coa_3rd = $_POST["idx_coa_3rd"];
        $remark_coa_3rd = $_POST["remark_coa_3rd"];

        $sql = " SELECT * FROM mst_coa_l3 WHERE idx='" . $kode_coa_3rd . "'  ";
        $resultMax = DB::select($sql);

        if (count($resultMax) > 0) {
            $resultMsg = array("sts" => "N", "desc" => "Kode COA Sudah Ada ! Gunakan Kode Yang Lain ! ");
        } else {
            $sql = " INSERT INTO mst_coa_l3 VALUES ('" . $kode_coa_3rd . "','" . $kode_coa_2nd . "','" . $code_coa . "','" . $kop_id . "','" . strtoupper($nama_coa_3rd) . "','" . $remark_coa_3rd . "','" . $idx_coa_3rd . "','Y','" . $userIns . "',NOW()); ";
            $resultIns = DB::insert($sql);

            if ($resultIns > 0) {
                $resultMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resultMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        }
        return json_encode($resultMsg);
    }

    public function insTempAnggota(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $nik = $_POST["nik"];
        $no_koperasi = $_POST["no_koperasi"];
        $no_anggota = $_POST["no_anggota"];
        $no_anggota_internal = $_POST["no_anggota_internal"];
        $usernm = $_POST["usernm"];
        $pass = $_POST["pass"];
        $nama = $_POST["nama"];
        $no_karyawan = $_POST["no_karyawan"];
        $no_telp = $_POST["no_telp"];
        $photo = $_POST["photo"];
        $email = $_POST["email"];
        $pendidikan = $_POST["pendidikan"];
        $status_perkawinan = $_POST["status_perkawinan"];
        $jenis_kelamin = $_POST["jenis_kelamin"];
        $tempat_lahir = $_POST["tempat_lahir"];
        $tanggal_lahir = $_POST["tanggal_lahir"];
        $tanggal_masuk = $_POST["tanggal_masuk"];
        $alamat = $_POST["alamat"];
        $kode_pos = $_POST["kode_pos"];
        $pekerjaan = $_POST["pekerjaan"];
        $agama = $_POST["agama"];
        $hubungan_kerabat = $_POST["hubungan_kerabat"];
        $no_telp_kerabat = $_POST["no_telp_kerabat"];
        $kelompok = $_POST["kelompok"];
        $nama_ibu_kandung = $_POST["nama_ibu_kandung"];

        $sql = " SELECT * FROM mst_anggota_temp WHERE usernm='" . $usernm . "' AND no_koperasi='" . $no_koperasi . "'  ";
        $resutlUsernm = DB::select($sql);
        if (count($resutlUsernm) > 0) {
            //$resultMsg = array("sts" => "N", "desc" => "User Name Sudah Ada", "msg" => "Harap Pilih Username Lain");
            $sql = "update   mst_anggota_temp set
      nik='" . $nik . "',
      no_anggota_internal='" . $no_anggota_internal . "',

      pass='" . $pass . "',
      nama='" . $nama . "',
      no_karyawan='" . $no_karyawan . "',
      no_telp='" . $no_telp . "',
      email='" . $email . "',
      pendidikan='" . $pendidikan . "',
      status_perkawinan='" . $status_perkawinan . "',
      jenis_kelamin='" . $jenis_kelamin . "',
      tempat_lahir='" . $tempat_lahir . "',
      tanggal_lahir='" . $tanggal_lahir . "',
      tanggal_masuk='" . $tanggal_masuk . "',
      alamat='" . $alamat . "',
      kode_pos='" . $kode_pos . "',
      pekerjaan='" . $pekerjaan . "',
      agama='" . $agama . "',
      hubungan_kerabat='" . $hubungan_kerabat . "',
      no_telp_kerabat='" . $no_telp_kerabat . "',
      kelompok='" . $kelompok . "',
      nama_ibu_kandung='" . $nama_ibu_kandung . "'
      where no_anggota='" . $no_anggota . "'
      ";
            //var_dump($sql);
            $resultUpd = DB::update($sql);
            $resultMsg = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
        } else {

            $sql = "select * from mst_anggota_temp where no_anggota='" . $no_anggota . "'  ";
            $resutlMax = DB::select($sql);
            if (count($resutlMax) > 0) {
                $sql = "update   mst_anggota_temp set
          nik='" . $nik . "',
          no_anggota_internal='" . $no_anggota_internal . "',
          usernm='" . $usernm . "',
          pass='" . $pass . "',
          nama='" . $nama . "',
          no_karyawan='" . $no_karyawan . "',
          no_telp='" . $no_telp . "',
          email='" . $email . "',
          pendidikan='" . $pendidikan . "',
          status_perkawinan='" . $status_perkawinan . "',
          jenis_kelamin='" . $jenis_kelamin . "',
          tempat_lahir='" . $tempat_lahir . "',
          tanggal_lahir='" . $tanggal_lahir . "',
          tanggal_masuk='" . $tanggal_masuk . "',
          alamat='" . $alamat . "',
          kode_pos='" . $kode_pos . "',
          pekerjaan='" . $pekerjaan . "',
          agama='" . $agama . "',
          hubungan_kerabat='" . $hubungan_kerabat . "',
          no_telp_kerabat='" . $no_telp_kerabat . "',
          kelompok='" . $kelompok . "',
          nama_ibu_kandung='" . $nama_ibu_kandung . "'
          where no_anggota='" . $no_anggota . "'
          ";
                //var_dump($sql);
                $resultUpd = DB::update($sql);
                $resultMsg = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
            } else {

                $sql = "
            insert into mst_anggota_temp(nik,no_koperasi,no_anggota,no_anggota_internal,usernm,pass,nama,no_karyawan,
              no_telp,photo,email,pendidikan,status_perkawinan,jenis_kelamin,
              tempat_lahir,tanggal_lahir,tanggal_masuk,alamat,kode_pos,pekerjaan,pekerjaan_detail,agama,hubungan_kerabat,no_telp_kerabat,kelompok,nama_ibu_kandung,insUser,insDt
              )
              values ( '" . $nik . "','" . $no_koperasi . "','" . $no_anggota . "','" . $no_anggota_internal . "','" . $usernm . "','" . $pass . "','" . $nama . "','" . $no_karyawan . "',
              '" . $no_telp . "','" . $photo . "','" . $email . "','" . $pendidikan . "','" . $status_perkawinan . "','" . $jenis_kelamin . "',
              '" . $tempat_lahir . "','" . $tanggal_lahir . "','" . $tanggal_masuk . "','" . $alamat . "','" . $kode_pos . "','" . $pekerjaan . "','','" . $agama . "',
              '" . $hubungan_kerabat . "','" . $no_telp_kerabat . "','" . $kelompok . "','" . $nama_ibu_kandung . "','" . $userIns . "',now()
              )
            ";

                //var_dump($sql);
                $resultIns = DB::insert($sql);

                if ($resultIns > 0) {
                    $resultMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
                } else {
                    $resultMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
                }
            }
        }
        return json_encode($resultMsg);
    }

    public function InsCodeDetail(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kopId = $_POST["kop_id"];
        $useYn = $_POST["useYn"];
        $masterCode = $_POST["masterCode"];
        $detailDesc = $_POST["detailDesc"];
        $detailName2 = $_POST["detailName2"];
        $detailName = $_POST["detailName"];
        $detailCode = $_POST["detailCode"];

        $sql = "select * from mst_code_detail  where detailCode='" . $detailCode . "' and masterCode='" . $masterCode . "'  ";
        $resutlMax = DB::select($sql);

        if (count($resutlMax) > 0) {
            $sql = "
            update  mst_code_detail set detailName='" . $detailName . "' ,detailName2='" . $detailName2 . "', detailDesc='" . $detailDesc . "'
            where detailCode='" . $detailCode . "' and masterCode='" . $masterCode . "' and detailKop='" . $kopId . "'
        ";
            $resultIns = DB::update($sql);
            $resultMsg = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
        } else {
            $sql = "
        SELECT
        ifnull(CASE
        when length(max(detailcode)+1)=1 then CONCAT('00000',max(detailcode)+1 )
        when length(max(detailcode)+1)=2 then CONCAT('0000',max(detailcode)+1 )
        when length(max(detailcode)+1)=3 then CONCAT('000',max(detailcode)+1 )
        when length(max(detailcode)+1)=4 then CONCAT('00',max(detailcode)+1 )
        when length(max(detailcode)+1)=5 then CONCAT('0',max(detailcode)+1 )
        ELSE max(detailcode)+1
        END,'000001')  noCodeDetail,ifnull(max(detailcode)+1,1) detailIdx
        from mst_code_detail  WHERE masterCode='" . $masterCode . "' AND detailKop LIKE '%" . $kopId . "%'
        ";
            $resultMax = DB::select($sql);

            $sql = "
            INSERT INTO  mst_code_detail VALUE('" . $masterCode . "','" . $resultMax[0]->noCodeDetail . "','" . $detailName . "','" . $detailName2 . "','" . $resultMax[0]->detailIdx . "',
            '" . $useYn . "','" . $kopId . "','" . $detailDesc . "','" . $useYn . "',NOW(),'" . $userIns . "')
        ";
            $resultIns = DB::insert($sql);
            if ($resultIns > 0) {
                $resultMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resultMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        }
        return json_encode($resultMsg);
    }

    public function appDaftarAnggota(Request $request, $cmd)
    {
        $no_anggota = $_POST["no_anggota"];
        $kop_id = $_POST["kop_id"];
        $sql = "select * from mst_anggota_temp where no_anggota='" . $no_anggota . "'  and no_koperasi='" . $kop_id . "' ";
        $resutlMax = DB::select($sql);

        if (count($resutlMax) > 0) {
            $sql = "  INSERT INTO mst_anggota
        SELECT nik,no_koperasi,no_anggota,no_anggota_internal,usernm,pass,nama,no_karyawan,
        no_telp,photo,email,pendidikan,status_perkawinan,jenis_kelamin,tempat_lahir,tanggal_lahir,
        alamat,tanggal_masuk,kode_pos,pekerjaan,pekerjaan_detail,
        agama,hubungan_kerabat,no_telp_kerabat,kelompok,nama_ibu_kandung,insUser,
        insDt FROM mst_anggota_temp where no_anggota='" . $no_anggota . "'  ";

            $resultIns = DB::delete($sql);

            if ($resultIns > 0) {
                $sql = " delete from  mst_anggota_temp where no_anggota='" . $no_anggota . "' and no_koperasi='" . $kop_id . "' ";
                $resultIns1 = DB::delete($sql);
                $resultMsg = array("sts" => "OK", "desc" => "Migrasi Data Success", "msg" => "");
            } else {
                $resultMsg = array("sts" => "N", "desc" => "Migrasi Data Failed", "msg" => $resultIns);
            }
        } else {
            $resultMsg = array("sts" => "N", "desc" => "No Anggota Tidak Ada ! Silahkan Gunakan No Anggota Lain");
        }
        return json_encode($resultMsg);
    }

    public function delAnggota(Request $request, $cmd)
    {
        $no_anggota = $_POST["no_anggota"];
        $kop_id = $_POST["kop_id"];
        $sql = "select * from mst_anggota where no_anggota='" . $no_anggota . "' and no_koperasi='" . $kop_id . "' ";
        $resutlMax = DB::select($sql);

        if (count($resutlMax) > 0) {
            $sql = "delete from  mst_anggota where no_anggota='" . $no_anggota . "' and no_koperasi='" . $kop_id . "' ";
            $resultIns = DB::delete($sql);

            if ($resultIns > 0) {
                $resultMsg = array("sts" => "OK", "desc" => "Delete Success", "msg" => "");
            } else {
                $resultMsg = array("sts" => "N", "desc" => "Delete Failed", "msg" => $resultIns);
            }
        } else {
            $resultMsg = array("sts" => "N", "desc" => "No Anggota Tidak Ada ! Silahkan Gunakan No Anggota Lain");
        }
        return json_encode($resultMsg);
    }

    public function delTempAnggota(Request $request, $cmd)
    {
        $no_anggota = $_POST["no_anggota"];
        $kop_id = $_POST["kop_id"];
        $sql = "select * from mst_anggota_temp where no_anggota='" . $no_anggota . "' and no_koperasi='" . $kop_id . "' ";
        $resutlMax = DB::select($sql);

        if (count($resutlMax) > 0) {
            $sql = "delete from  mst_anggota_temp where no_anggota='" . $no_anggota . "' and no_koperasi='" . $kop_id . "' ";
            $resultIns = DB::delete($sql);

            if ($resultIns > 0) {
                $resultMsg = array("sts" => "OK", "desc" => "Delete Success", "msg" => "");
            } else {
                $resultMsg = array("sts" => "N", "desc" => "Delete Failed", "msg" => $resultIns);
            }
        } else {
            $resultMsg = array("sts" => "N", "desc" => "No Anggota Tidak Ada ! Silahkan Gunakan No Anggota Lain");
        }
        return json_encode($resultMsg);
    }

    public function insAnggota(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $nik = $_POST["nik"];
        $no_koperasi = $_POST["no_koperasi"];
        $no_anggota = $_POST["no_anggota"];
        $no_anggota_internal = $_POST["no_anggota_internal"];
        $usernm = $_POST["usernm"];
        $pass = $_POST["pass"];
        $nama = $_POST["nama"];
        $no_karyawan = $_POST["no_karyawan"];
        $no_telp = $_POST["no_telp"];
        $photo = $_POST["photo"];
        $email = $_POST["email"];
        $pendidikan = $_POST["pendidikan"];
        $status_perkawinan = $_POST["status_perkawinan"];
        $jenis_kelamin = $_POST["jenis_kelamin"];
        $tempat_lahir = $_POST["tempat_lahir"];
        $tanggal_lahir = $_POST["tanggal_lahir"];
        $tanggal_masuk = $_POST["tanggal_masuk"];
        $alamat = $_POST["alamat"];
        $kode_pos = $_POST["kode_pos"];
        $pekerjaan = $_POST["pekerjaan"];
        $agama = $_POST["agama"];
        $hubungan_kerabat = $_POST["hubungan_kerabat"];
        $no_telp_kerabat = $_POST["no_telp_kerabat"];
        $kelompok = $_POST["kelompok"];
        $nama_ibu_kandung = $_POST["nama_ibu_kandung"];

        $sql = " SELECT * FROM mst_anggota WHERE usernm='" . $usernm . "' AND no_koperasi='" . $no_koperasi . "'  ";
        $resutlUsernm = DB::select($sql);
        if (count($resutlUsernm) > 0) {
            //$resultMsg = array("sts" => "N", "desc" => "User Name Sudah Ada", "msg" => "Harap Pilih Username Lain");
            $sql = "update   mst_anggota set
      nik='" . $nik . "',
      no_anggota_internal='" . $no_anggota_internal . "',

      pass='" . $pass . "',
      nama='" . $nama . "',
      no_karyawan='" . $no_karyawan . "',
      no_telp='" . $no_telp . "',
      email='" . $email . "',
      pendidikan='" . $pendidikan . "',
      status_perkawinan='" . $status_perkawinan . "',
      jenis_kelamin='" . $jenis_kelamin . "',
      tempat_lahir='" . $tempat_lahir . "',
      tanggal_lahir='" . $tanggal_lahir . "',
      tanggal_masuk='" . $tanggal_masuk . "',
      alamat='" . $alamat . "',
      kode_pos='" . $kode_pos . "',
      pekerjaan='" . $pekerjaan . "',
      agama='" . $agama . "',
      hubungan_kerabat='" . $hubungan_kerabat . "',
      no_telp_kerabat='" . $no_telp_kerabat . "',
      kelompok='" . $kelompok . "',
      nama_ibu_kandung='" . $nama_ibu_kandung . "'
      where no_anggota='" . $no_anggota . "'
      ";
            //var_dump($sql);
            $resultUpd = DB::update($sql);
            $resultMsg = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
        } else {

            $sql = "select * from mst_anggota where no_anggota='" . $no_anggota . "'  ";
            $resutlMax = DB::select($sql);
            if (count($resutlMax) > 0) {
                $sql = "update   mst_anggota set
          nik='" . $nik . "',
          no_anggota_internal='" . $no_anggota_internal . "',
          usernm='" . $usernm . "',
          pass='" . $pass . "',
          nama='" . $nama . "',
          no_karyawan='" . $no_karyawan . "',
          no_telp='" . $no_telp . "',
          email='" . $email . "',
          pendidikan='" . $pendidikan . "',
          status_perkawinan='" . $status_perkawinan . "',
          jenis_kelamin='" . $jenis_kelamin . "',
          tempat_lahir='" . $tempat_lahir . "',
          tanggal_lahir='" . $tanggal_lahir . "',
          tanggal_masuk='" . $tanggal_masuk . "',
          alamat='" . $alamat . "',
          kode_pos='" . $kode_pos . "',
          pekerjaan='" . $pekerjaan . "',
          agama='" . $agama . "',
          hubungan_kerabat='" . $hubungan_kerabat . "',
          no_telp_kerabat='" . $no_telp_kerabat . "',
          kelompok='" . $kelompok . "',
          nama_ibu_kandung='" . $nama_ibu_kandung . "'
          where no_anggota='" . $no_anggota . "'
          ";
                //var_dump($sql);
                $resultUpd = DB::update($sql);
                $resultMsg = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
            } else {

                $sql = "
            insert into mst_anggota(nik,no_koperasi,no_anggota,no_anggota_internal,usernm,pass,nama,no_karyawan,
              no_telp,photo,email,pendidikan,status_perkawinan,jenis_kelamin,
              tempat_lahir,tanggal_lahir,tanggal_masuk,alamat,kode_pos,pekerjaan,pekerjaan_detail,agama,hubungan_kerabat,no_telp_kerabat,kelompok,nama_ibu_kandung,insUser,insDt
              )
              values ( '" . $nik . "','" . $no_koperasi . "','" . $no_anggota . "','" . $no_anggota_internal . "','" . $usernm . "','" . $pass . "','" . $nama . "','" . $no_karyawan . "',
              '" . $no_telp . "','" . $photo . "','" . $email . "','" . $pendidikan . "','" . $status_perkawinan . "','" . $jenis_kelamin . "',
              '" . $tempat_lahir . "','" . $tanggal_lahir . "','" . $tanggal_masuk . "','" . $alamat . "','" . $kode_pos . "','" . $pekerjaan . "','','" . $agama . "',
              '" . $hubungan_kerabat . "','" . $no_telp_kerabat . "','" . $kelompok . "','" . $nama_ibu_kandung . "','" . $userIns . "',now()
              )
            ";

                //var_dump($sql);
                $resultIns = DB::insert($sql);

                if ($resultIns > 0) {
                    $resultMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
                } else {
                    $resultMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
                }
            }
        }
        return json_encode($resultMsg);
    }



    public function InsRegKoperasi(Request $request, $cmd)
    {

        $pktr = $_POST["pktr"];
        $nama_koperasi = $_POST["nama_koperasi"];
        $no_telp = $_POST["no_telp"];
        $no_hp = $_POST["no_hp"];
        $no_fax = $_POST["no_fax"];
        $jenis_koperasi = $_POST["jenis_koperasi"];
        $alamat = $_POST["alamat"];
        $kode_pos = $_POST["kode_pos"];
        $email = $_POST["email"];
        $website = $_POST["website"];
        $akta_pendirian_nomor = $_POST["akta_pendirian_nomor"];
        $akta_pendirian_tanggal = $_POST["akta_pendirian_tanggal"];
        $akta_perubahan_nomor = $_POST["akta_perubahan_nomor"];
        $akta_perubahan_tanggal = $_POST["akta_perubahan_tanggal"];
        $sk_kemenkumham_nomor = $_POST["sk_kemenkumham_nomor"];
        $sk_kemenkumham_bulan = $_POST["sk_kemenkumham_bulan"];
        $sk_kemenkumham_tanggal = $_POST["sk_kemenkumham_tanggal"];
        $sk_kemenkumham_tahun = $_POST["sk_kemenkumham_tahun"];
        $no_surat_pengesahan_nomor = $_POST["no_surat_pengesahan_nomor"];
        $no_surat_pengesahan_bulan = $_POST["no_surat_pengesahan_bulan"];
        $no_surat_pengesahan_tanggal = $_POST["no_surat_pengesahan_tanggal"];
        $no_surat_pengesahan_tahun = $_POST["no_surat_pengesahan_tahun"];
        $siup_nomor = $_POST["siup_nomor"];
        $tdp_nomor = $_POST["tdp_nomor"];
        $tdp_masaberlaku = $_POST["tdp_masaberlaku"];
        $surat_keterangan_domisili_nomor = $_POST["surat_keterangan_domisili_nomor"];
        $surat_keterangan_domisili_masaberlaku = $_POST["surat_keterangan_domisili_masaberlaku"];
        $bpjs_kesehatan = $_POST["bpjs_kesehatan"];
        $bpjs_ketenagakerjaan = $_POST["bpjs_ketenagakerjaan"];
        $no_sertifikat_koperasi = $_POST["no_sertifikat_koperasi"];




        $sql = "select * from mst_koperasi where pk='" . $pktr . "'  ";
        $resultMax = DB::select($sql);

        if (count($resultMax) > 0) {
            $result = array("sts" => "N", "desc" => "User Id Sudah Ada ! Silahkan Gunakan Userid Lain");
        } else {

            $sql = "insert into mst_koperasi values ('" . $pktr . "','',
            '" . $nama_koperasi . "',
            '" . $no_telp . "',
            '" . $no_hp . "',
            '" . $no_fax . "',
            '" . $jenis_koperasi . "',
            '" . $alamat . "',
            '" . $kode_pos . "',
            '" . $email . "',
            '" . $website . "',
            '" . $akta_pendirian_nomor . "',
            '" . $akta_pendirian_tanggal . "',
            '" . $akta_perubahan_nomor . "',
            '" . $akta_perubahan_tanggal . "',
            '" . $sk_kemenkumham_nomor . "',
            '" . $sk_kemenkumham_bulan . "',
            '" . $sk_kemenkumham_tanggal . "',
            '" . $sk_kemenkumham_tahun . "',
            '" . $no_surat_pengesahan_nomor . "',
            '" . $no_surat_pengesahan_bulan . "',
            '" . $no_surat_pengesahan_tanggal . "',
            '" . $no_surat_pengesahan_tahun . "',
            '" . $siup_nomor . "',
            '" . $tdp_nomor . "',
            '" . $tdp_masaberlaku . "',
            '" . $surat_keterangan_domisili_nomor . "',
            '" . $surat_keterangan_domisili_masaberlaku . "',
            '" . $bpjs_kesehatan . "',
            '" . $bpjs_ketenagakerjaan . "',
            '" . $no_sertifikat_koperasi . "','Y','',now() ) ";



            $resultsIns = DB::insert($sql);
            if ($resultsIns > 0) {
                $result = array("sts" => "OK", "desc" => "Save Success", "msg" => $pktr);
            } else {
                $result = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultsIns);
            }
        }
        return json_encode($result);
    }

    public function delBarang(Request $request, $cmd)
    {
        $idx = $_POST["idx"];
        $sql = " DELETE FROM  mst_barang  WHERE idx=?  ";
        $results = DB::delete($sql, [$idx]);

        if ($results > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }

    public function delGudang(Request $request, $cmd)
    {
        $kode_gudang = $_POST["kode_gudang"];
        $sql = " DELETE FROM  mst_gudang  WHERE kode_gudang=?  ";
        $results = DB::delete($sql, [$kode_gudang]);

        if ($results > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }

    public function InsGudang(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kopId = $_POST["kopId"];
        $kode_gudang = $_POST["kode_gudang"];
        $nama_gudang = $_POST["nama_gudang"];
        $alamat = $_POST["alamat"];
        $nomor_telpon = $_POST["nomor_telpon"];
        $status = $_POST["status"];

        $sql = "select * from mst_gudang where kode_gudang='" . $kode_gudang . "'  ";
        $resultMax = DB::select($sql);

        if (count($resultMax) > 0) {
            $result = array("sts" => "N", "desc" => "Id Sudah Ada ! Silahkan Gunakan Id Lain");
        } else {
            $sql = "insert into mst_gudang values ('" . $kopId . "',
            '" . $kode_gudang . "',
            '" . $nama_gudang . "',
            '" . $alamat . "',
            '" . $nomor_telpon . "',
            '" . $status . "','" . $userIns . "',now() ) ";

            $resultsIns = DB::insert($sql);
            if ($resultsIns > 0) {
                $result = array("sts" => "OK", "desc" => "Save Success", "msg" => $kode_gudang);
            } else {
                $result = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultsIns);
            }
        }
        return json_encode($result);
    }

    public function delPembeli(Request $request, $cmd)
    {
        $kode_pembeli = $_POST["kode_pembeli"];
        $sql = " DELETE FROM  mst_pembeli  WHERE kode_pembeli=?  ";
        $results = DB::delete($sql, [$kode_pembeli]);

        if ($results > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }

    public function InsPembeli(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kopId = $_POST["kopId"];
        $kode_pembeli = $_POST["kode_pembeli"];
        $nama_pembeli = $_POST["nama_pembeli"];
        $alamat = $_POST["alamat"];
        $nomor_telpon = $_POST["nomor_telpon"];
        $status = $_POST["status"];

        $sql = "select * from mst_pembeli where kode_pembeli='" . $kode_pembeli . "'  ";
        $resultMax = DB::select($sql);

        if (count($resultMax) > 0) {
            $result = array("sts" => "N", "desc" => "Id Sudah Ada ! Silahkan Gunakan Id Lain");
        } else {
            $sql = "insert into mst_pembeli values ('" . $kopId . "',
            '" . $kode_pembeli . "',
            '" . $nama_pembeli . "',
            '" . $alamat . "',
            '" . $nomor_telpon . "',
            '" . $status . "','" . $userIns . "',now() ) ";

            $resultsIns = DB::insert($sql);
            if ($resultsIns > 0) {
                $result = array("sts" => "OK", "desc" => "Save Success", "msg" => $kode_pembeli);
            } else {
                $result = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultsIns);
            }
        }
        return json_encode($result);
    }
    public function delSuplier(Request $request, $cmd)
    {
        $kode_suplier = $_POST["kode_suplier"];
        $sql = " DELETE FROM  mst_suplier  WHERE kode_suplier=?  ";
        $results = DB::delete($sql, [$kode_suplier]);

        if ($results > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }

    public function InsSuplier(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kop_d = $_POST["kopId"];
        $kode_suplier = $_POST["kode_suplier"];
        $nama_suplier = $_POST["nama_suplier"];
        $alamat = $_POST["alamat"];
        $nomor_telpon = $_POST["nomor_telpon"];
        $status = $_POST["status"];

        $sqlCheck = "SELECT COUNT(*) as count FROM mst_suplier WHERE kode_suplier = '" . $kode_suplier . "'";
        $resultMax = DB::select($sqlCheck);
        $count = $resultMax[0]->count;

        if ($count > 0) {
            // Jika ID sudah ada, lakukan update tanpa kopId
            $sql = "
        UPDATE mst_suplier
    SET
        nama_suplier = '" . $nama_suplier . "',
        alamat = '" . $alamat . "',
        nomor_telpon = '" . $nomor_telpon . "',
        status = '" . $status . "',
        dateUpd = now()
    WHERE kode_suplier = '" . $kode_suplier . "'
";

            $resultsUpd = DB::update($sql);
            if ($resultsUpd > 0) {
                $result = array("sts" => "OK", "desc" => "Update Success", "msg" => $kode_suplier);
            } else {
                $result = array("sts" => "N", "desc" => "Update Failed", "msg" => $resultsUpd);
            }
        } else {
            // Jika ID tidak ada, lakukan insert tanpa kopId
            $sql = "
        INSERT INTO mst_suplier
        (kode_suplier, nama_suplier, alamat, nomor_telpon, status, userIns, dateIns)
        VALUES
        ('" . $kode_suplier . "', '" . $nama_suplier . "', '" . $alamat . "',
        '" . $nomor_telpon . "', '" . $status . "', '" . $userIns . "', now())
    ";

            $resultsIns = DB::insert($sql);
            if ($resultsIns > 0) {
                $result = array("sts" => "OK", "desc" => "Insert Success", "msg" => $kode_suplier);
            } else {
                $result = array("sts" => "N", "desc" => "Insert Failed", "msg" => $resultsIns);
            }
        }

        return json_encode($result);
    }

    public function insKoperasi(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kop_id = $_POST["kop_id"];
        $nama_koperasi = $_POST["nama_koperasi"];
        $alamat = $_POST["alamat"];
        $no_telp = $_POST["no_telp"];
        $aktif = $_POST["aktif"];
        $jenis_koperasi = $_POST["jenis_koperasi"];
        $no_account = $_POST["no_account"];
        $sql = "select * from mst_koperasi where idx='" . $kop_id . "'  ";
        $resultMax = DB::select($sql);

        if (count($resultMax) > 0) {
            //$result = array("sts" => "N", "desc" => "Id Sudah Ada ! Silahkan Gunakan Id Lain");
            $sql = "update  mst_koperasi set nama_koperasi='" . $nama_koperasi . "',
            alamat='" . $alamat . "',
            no_telp= '" . $no_telp . "',
            jenis_koperasi='" . $jenis_koperasi . "',
            noAccount='" . $no_account . "',
            aktif='" . $aktif . "'
            where idx='" . $kop_id . "'
            ";
            //var_dump($sql);
            $resultsIns = DB::update($sql);
            $result = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
        } else {
            $sql = "insert into mst_koperasi (idx,nama_koperasi,alamat,no_telp,aktif,insUser,jenis_koperasi,InsDt,noAccount) values ('" . $kop_id . "',
            '" . $nama_koperasi . "',
            '" . $alamat . "',
            '" . $no_telp . "',
            '" . $aktif . "','" . $userIns . "','" . $jenis_koperasi . "',now(),'" . $no_account . "' ) ";

            $resultsIns = DB::insert($sql);
            if ($resultsIns > 0) {
                $result = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $result = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultsIns);
            }
        }
        return json_encode($result);
    }

    public function delKoperasi(Request $request, $cmd)
    {
        $idx = $_POST["idx"];
        $sql = " DELETE FROM  mst_koperasi  WHERE idx=?  ";
        $results = DB::delete($sql, [$idx]);

        if ($results > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }


    public function insToko(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $toko_id = $_POST["toko_id"];
        $kop_id = $_POST["kop_id"];
        $nama_toko = $_POST["nama_toko"];
        $alamat = $_POST["alamat"];
        $no_telp = $_POST["no_telp"];
        $aktif = $_POST["aktif"];
        $jenis_toko = $_POST["jenis_toko"];
        $sql = "select * from mst_toko where toko_id='" . $toko_id . "'  ";
        $resultMax = DB::select($sql);

        if (count($resultMax) > 0) {
            //$result = array("sts" => "N", "desc" => "Id Sudah Ada ! Silahkan Gunakan Id Lain");
            $sql = "update  mst_toko set nama_toko='" . $nama_toko . "',
            alamat='" . $alamat . "',
            no_telp= '" . $no_telp . "',
            jenis_toko='" . $jenis_toko . "',
            aktif='" . $aktif . "'
            where toko_id='" . $toko_id . "'
            ";
            //var_dump($sql);
            $resultsIns = DB::update($sql);
            $result = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
        } else {
            $sql = "insert into mst_toko (toko_id,kop_id,nama_toko,alamat,no_telp,aktif,insUser,jenis_toko,InsDt) values ('" . $toko_id . "','" . $kop_id . "',
            '" . $nama_toko . "',
            '" . $alamat . "',
            '" . $no_telp . "',
            '" . $aktif . "','" . $userIns . "','" . $jenis_toko . "',now() ) ";

            $resultsIns = DB::insert($sql);
            if ($resultsIns > 0) {
                $result = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $result = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultsIns);
            }
        }
        return json_encode($result);
    }
    public function delToko(Request $request, $cmd)
    {
        $toko_id = $_POST["toko_id"];
        $sql = " DELETE FROM  mst_toko  WHERE toko_id=?  ";
        $results = DB::delete($sql, [$toko_id]);

        if ($results > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }
    public function insBarangRutin(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kop_id = $_POST["kop_id"];
        $toko_id = $_POST["toko_id"];
        $kode_barang = $_POST["kode_barang"];
        $harga = $_POST["harga"];
        $barcode = $_POST["barcode"];
        $tgl_produksi = $_POST["tgl_produksi"];
        $tgl_expired = $_POST["tgl_expired"];
        $barcode_qty = $_POST["barcode_qty"];
        $packing_qty = $_POST["packing_qty"];

        $sql = "select * from mst_barang_rutin where barcode='" . $barcode . "'  ";
        $resultMax = DB::select($sql);
        if (count($resultMax) > 0) {
            $result = array("sts" => "N", "desc" => "Barcode Id Sudah Ada ! Silahkan Gunakan Id Lain");
        } else {
            $sql = " insert into mst_barang_rutin (kode_barang,kop_id,toko_id,barcode,tanggal_produksi,tanggal_kadaluarsa,harga,barcode_qty,packing_qty,insUser,insDate)
                values ('" . $kode_barang . "','" . $kop_id . "',
                '" . $toko_id . "','" . $barcode . "',
                '" . $tgl_produksi . "','" . $tgl_expired . "',
                '" . $harga . "','" . $barcode_qty . "',
                '" . $packing_qty . "',
                '" . $userIns . "',now() ) ";
            $resultsIns = DB::insert($sql);
            if ($resultsIns > 0) {
                $sql = " ";
                for ($x = 1; $x <= $barcode_qty; $x++) {
                    $sql = " insert into mst_barang_rutin_detail (kode_barang,kop_id,toko_id,barcode,generate_barcode,sequence,packing_qty,insUser,insDate)  values('" . $kode_barang . "','" . $kop_id . "',
          '" . $toko_id . "','" . $barcode . "','" . $barcode . $x . "B','" . $x . "','" . $packing_qty . "','" . $userIns . "',now())
          ";
                    DB::insert($sql);
                }
            }

            if ($resultsIns > 0) {
                $result = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $result = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultsIns);
            }
        }
        return json_encode($result);
    }
    public function delBarangRutin(Request $request, $cmd)
    {
        $toko_id = $_POST["toko_id"];
        $sql = " DELETE FROM  mst_toko  WHERE toko_id=?  ";
        $results = DB::delete($sql, [$toko_id]);

        if ($results > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }

    public function InsFelloPrice(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $kop_id = $_POST["kop_id"];

        $tipe = $_POST["tipe"];
        $tipe_name = $_POST["tipe_name"];
        $kode_layanan = $_POST["kode_layanan"];
        $kode_biller = $_POST["kode_biller"];
        $fello_product_id = $_POST["fello_product_id"];
        $product = $_POST["product"];
        $biller_name = $_POST["biller_name"];
        $product_name = $_POST["product_name"];
        $denum = $_POST["denum"];
        $harga = $_POST["harga"];
        $harga_modal = $_POST["harga_modal"];





        $sql = "select * from mst_fello_multi_biller where kop_id='" . $kop_id . "' and concat_id='" . $product . "'  ";
        $resultMax = DB::select($sql);

        if (count($resultMax) > 0) {
            $sql = "
        UPDATE  mst_fello_multi_biller SET
        denum='" . $denum . "',
        harga_modal='" . $harga_modal . "',
        harga='" . $harga . "'
        where kop_id='" . $kop_id . "' and concat_id='" . $product . "' ;
        ";


            $resultIns = DB::update($sql);
            $resultMsg = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
        } else {

            $sql = "
            insert into mst_fello_multi_biller values ('" . $kop_id . "','" . $tipe . "','" . $tipe_name . "','" . $kode_layanan . "','" . $kode_biller . "','" . $fello_product_id . "',
            '" . $product . "','" . $denum . "','" . $harga_modal . "','" . $harga . "','" . $biller_name . "',
            '" . $product_name . "','" . $userIns . "',now() )
          ";

            $resultIns = DB::insert($sql);
            if ($resultIns == "OK") {
                $resultMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => "");
            } else {
                $resultMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
            }
        }
        return json_encode($resultMsg);
    }


    public function delPriceFello(Request $request, $cmd)
    {
        $kop_id = $_POST["kop_id"];
        $concat_id = $_POST["concat_id"];
        $sql = " DELETE FROM  mst_fello_multi_biller  WHERE concat_id=? and kop_id=? ";
        $results = DB::delete($sql, [$concat_id, $kop_id]);

        if ($results > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }
}
