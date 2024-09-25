<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryMasterController extends Controller
{
    //
    public function run(Request $request)
    {
        $cmd = $request->get("cmd");
        if ($cmd == "selUser") {
            $result = $this->selUser($request, $cmd);
        } else if ($cmd == "selTipeUser") {
            $result = $this->selTipeUser($request, $cmd);
        } else if ($cmd == "selCoaGroup") {
            $result = $this->selCoaGroup($request, $cmd);
        } else if ($cmd == "selAkun") {
            $result = $this->selAkun($request, $cmd);
        } else if ($cmd == "selCoaL2") {
            $result = $this->selCoaL2($request, $cmd);
        } else if ($cmd == "selCoaL3") {
            $result = $this->selCoaL3($request, $cmd);
        } else if ($cmd == "selUserNomorCoa") {
            $result = $this->selUserNomorCoa($request, $cmd);
        } else if ($cmd == "selNomorKodeBarang") {
            $result = $this->selNomorKodeBarang($request, $cmd);
        } else if ($cmd == "selUserNomorCoa2nd") {
            $result = $this->selUserNomorCoa2nd($request, $cmd);
        } else if ($cmd == "selUserNomorCoa3rd") {
            $result = $this->selUserNomorCoa3rd($request, $cmd);
        } else if ($cmd == "selUserNomor") {
            $result = $this->selUserNomor($request, $cmd);
        } else if ($cmd == "selBarang") {
            $result = $this->selBarang($request, $cmd);
        } else if ($cmd == "selCodeDetailBarang") {
            $result = $this->selCodeDetailBarang($request, $cmd);
        } else if ($cmd == "selCodeDetail") {
            $result = $this->selCodeDetail($request, $cmd);
        } else if ($cmd == "selCodeMaster") {
            $result = $this->selCodeMaster($request, $cmd);
        } else if ($cmd == "selAnggota") {
            $result = $this->selAnggota($request, $cmd);
        } else if ($cmd == "selAnggotaTemp") {
            $result = $this->selAnggotaTemp($request, $cmd);
        } else if ($cmd == "selLoginAnggota") {
            $result = $this->selLoginAnggota($request, $cmd);
        } else if ($cmd == "selKoperasi") {
            $result = $this->selKoperasi($request, $cmd);
        } else if ($cmd == "selDashboardRegister") {
            $result = $this->selDashboardRegister($request, $cmd);
        } else if ($cmd == "selAllCodeAnggota") {
            $result = $this->selAllCodeAnggota($request, $cmd);
        } else if ($cmd == "selAnggotaNomor") {
            $result = $this->selAnggotaNomor($request, $cmd);
        } else if ($cmd == "selGudang") {
            $result = $this->selGudang($request, $cmd);
        } else if ($cmd == "selGudangNomor") {
            $result = $this->selGudangNomor($request, $cmd);
        } else if ($cmd == "selPembeli") {
            $result = $this->selPembeli($request, $cmd);
        } else if ($cmd == "selPembeliNomor") {
            $result = $this->selPembeliNomor($request, $cmd);
        } else if ($cmd == "selSuplier") {
            $result = $this->selSuplier($request, $cmd);
        } else if ($cmd == "selSuplierNomor") {
            $result = $this->selSuplierNomor($request, $cmd);
        } else if ($cmd == "selJenisSimpanan") {
            $result = $this->selJenisSimpanan($request, $cmd);
        } else if ($cmd == "selKoperasiNomor") {
            $result = $this->selKoperasiNomor($request, $cmd);
        } else if ($cmd == "selJenisTranKas") {
            $result = $this->selJenisTranKas($request, $cmd);
        } else if ($cmd == "selTokoNomor") {
            $result = $this->selTokoNomor($request, $cmd);
        } else if ($cmd == "selToko") {
            $result = $this->selToko($request, $cmd);
        } else if ($cmd == "selTipeToko") {
            $result = $this->selTipeToko($request, $cmd);
        } else if ($cmd == "selHakUser") {
            $result = $this->selHakUser($request, $cmd);
        } else if ($cmd == "selBarcode") {
            $result = $this->selBarcode($request, $cmd);
        } else if ($cmd == "selBarcodeDetail") {
            $result = $this->selBarcodeDetail($request, $cmd);
        } else if ($cmd == "selPriceFello") {
            $result = $this->selPriceFello($request, $cmd);
        } else {
            $result = $this->noCmd($request, $cmd);
        }
        return $result;
    }

    public function noCmd(Request $request, $cmd)
    {
        $resutlMsg = array("sts" => "Error", "desc" => "No Command For This API ", "msg" => "No Command For This API");
        return json_encode($resutlMsg);
    }

    public function selUser(Request $request, $cmd)
    {
        $tipeUser = '%' .  $request->get("tipeUser") . '%';
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $sql = "
            SELECT *,ifnull(b.nama_koperasi,'')nama_koperasi,c.nama_toko FROM mst_user a
            LEFT JOIN mst_koperasi b ON a.no_koperasi=b.idx
            LEFT JOIN mst_toko c ON a.no_toko=c.toko_id
            where level like ?  and no_koperasi like ? and level<>'ADMINISTRATOR' order by a.namaUser
        ";
        $results = DB::select($sql, [$tipeUser, $kop_id]);
        return json_encode($results);
    }

    public function selHakUser(Request $request, $cmd)
    {
        $userId =   $request->get("userId");
        $sql = "
        SELECT * FROM sysmenu  a
        LEFT JOIN user_sysmenu b ON a.menuCode=b.menuId and b.userId=?
        LEFT JOIN mst_user c ON  b.userId=c.userid AND c.userId=?
        ";
        $results = DB::select($sql, [$userId, $userId]);
        return json_encode($results);
    }

    public function selTipeUser(Request $request, $cmd)
    {
        $sql = "";
        $sql = "
            SELECT *,detailName FROM mst_code_detail where detailcode not in ('000001','000005') AND masterCode='1000019'
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selTipeToko(Request $request, $cmd)
    {
        $sql = "
            SELECT * FROM mst_code_detail where  masterCode='1000021'
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selCoaGroup(Request $request, $cmd)
    {
        $sql = "
            SELECT *,level FROM mst_user GROUP BY level
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selAkun(Request $request, $cmd)
    {
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $sql = "
            SELECT *, b.detailName Coa_L1, c.detailName Coa_L2,d.nama_koperasi FROM
            mst_coa_group a
            JOIN mst_code_detail b ON a.coa1=b.detailCode AND b.masterCode='1000007'
            JOIN mst_code_detail c ON a.coa2=c.detailCode AND c.masterCode='1000008'
            LEFT JOIN mst_koperasi d ON a.kop_id=d.idx
            WHERE d.idx LIKE ?
            ORDER BY a.idx,a.coaidx
        ";
        $results = DB::select($sql, [$kop_id]);
        return json_encode($results);
    }

    public function selCoaL2(Request $request, $cmd)
    {
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $coa =  $request->get("coa") . '%';
        $sql = "
            SELECT * FROM mst_code_detail WHERE masterCode='1000008' and detailKop like ? and detailName2 like ?
        ";
        $results = DB::select($sql, [$kop_id, $coa]);
        return json_encode($results);
    }

    public function selCoaL3(Request $request, $cmd)
    {
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $sql = "
            SELECT *,b.idxd_l2,b.nama_detail_coa,b.coaIdx coaL2Idx,d.nama_koperasi,b.useYn detailUseYn,
            c.idxd_l3,c.nama_detail_l3_coa,c.desc_detail_l3_coa,c.coa3Idx,c.useYn aktifL3
            FROM mst_coa_group a
            JOIN mst_coa_l2 b ON a.idx=b.idx
            JOIN mst_coa_l3 c ON b.idxd_l2=c.idxd_l2
            LEFT JOIN mst_koperasi d ON a.kop_id=d.idx
            WHERE d.idx LIKE ?
            ORDER BY a.idx,a.coaidx , b.coaIdx ,c.coa3Idx
        ";
        $results = DB::select($sql, [$kop_id]);
        return json_encode($results);
    }

    public function selUserNomorCoa(Request $request, $cmd)
    {
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $sql = "
            SELECT *,CONCAT('COAG',max(REPLACE(idx,'COAG',''))+1 ) as noCoag,max(REPLACE(idx,'COAG',''))+1 coagNumber from mst_coa_group WHERE kop_id LIKE ?
        ";
        $results = DB::select($sql, [$kop_id]);
        return json_encode($results);
    }

    public function selNomorKodeBarang(Request $request, $cmd)
    {
        $sql = "
        SELECT *,ifnull(CONCAT('BRG-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),CONCAT('BRG-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noBrg,
        ifnull(max(convert(REPLACE('','BRG-',''),DECIMAL))+1,1) brgNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selUserNomorCoa2nd(Request $request, $cmd)
    {
        $coag = '%' .  $request->get("coag") . '%';
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $sql = "
            SELECT *,CONCAT('COAD',max(REPLACE(idxd_l2,'COAD',''))+1 ) as noCoad,max(REPLACE(idxd_l2,'COAD',''))+1 coadNumber from mst_coa_l2 WHERE idx LIKE ? AND kop_id LIKE ?
        ";
        $results = DB::select($sql, [$coag, $kop_id]);
        return json_encode($results);
    }

    public function selUserNomorCoa3rd(Request $request, $cmd)
    {
        $coads = '%' .  $request->get("coads") . '%';
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $sql = "
            SELECT *,CONCAT('COADS',max(REPLACE(idxd_l3,'COADS',''))+1 ) as noCoads,max(REPLACE(idxd_l3,'COADS',''))+1 coadsNumber from mst_coa_l3 WHERE idxd_l2 LIKE  ? AND kop_id LIKE ?
        ";
        $results = DB::select($sql, [$coads, $kop_id]);
        return json_encode($results);
    }

    public function selUserNomor(Request $request, $cmd)
    {
        $sql = "
        SELECT *,ifnull(CONCAT('USER-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),CONCAT('USER-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noUser,
        ifnull(max(convert(REPLACE('','USER-',''),DECIMAL))+1,1) userNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selKoperasiNomor(Request $request, $cmd)
    {
        $sql = "
            SELECT CONCAT('KOP-', DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')) as noKop,
            ifnull(max(convert(REPLACE('','KOP-',''),DECIMAL))+1,1) as kopNumber FROM mst_koperasi;
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selTokoNomor(Request $request, $cmd)
    {
        $sql = "
            SELECT *,ifnull(CONCAT('TOK-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),CONCAT('TOK-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noTok,
            ifnull(max(convert(REPLACE('','TOK-',''),DECIMAL))+1,1) tokNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selCodeDetailNomor(Request $request, $cmd)
    {
        $masterCode =  $request->get("masterCode");
        $kop_id = '%' .  $request->get("kop_id") . '%';

        $sql = "
            SELECT *,
            CASE
            when length(max(detailcode)+1)=1 then CONCAT('00000',max(detailcode)+1 )
            when length(max(detailcode)+1)=2 then CONCAT('0000',max(detailcode)+1 )
            when length(max(detailcode)+1)=3 then CONCAT('000',max(detailcode)+1 )
            when length(max(detailcode)+1)=4 then CONCAT('00',max(detailcode)+1 )
            when length(max(detailcode)+1)=5 then CONCAT('0',max(detailcode)+1 )
            ELSE max(detailcode)+1
            end  noCodeDetail
            from mst_code_detail  WHERE masterCode=? and detailKop like ?
        ";
        $results = DB::select($sql, [$masterCode, $kop_id]);
        return json_encode($results);
    }

    public function selCodeMaster(Request $request, $cmd)
    {
        $sql = "
            SELECT * FROM mst_code_master WHERE  masterCode<>'1000009'   ORDER BY masterCode
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selCodeDetail(Request $request, $cmd)
    {
        $kop_id =  $request->get("kop_id");
        $masterCode =  $request->get("masterCode");
        $sql = "
        SELECT *,b.nama_koperasi
        FROM mst_code_detail a
        LEFT JOIN mst_koperasi b ON a.detailKop=b.idx  WHERE  masterCode<>'1000009' and masterCode like '" . $masterCode . "%'
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selCodeDetailBarang(Request $request, $cmd)
    {
        $kop_id =  $request->get("kop_id");
        $sql = "
            SELECT * FROM mst_code_detail a WHERE  masterCode IN ('1000013')AND detailkop LIKE '%" . $kop_id . "%'
            UNION all
            SELECT * FROM mst_code_detail a WHERE  masterCode IN ('1000014')
            UNION all
            SELECT * FROM mst_code_detail a WHERE  masterCode IN ('1000016') AND detailkop LIKE '%" . $kop_id . "%'
            ORDER BY masterCode,detailCode
          ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selBarang(Request $request, $cmd)
    {
        $kop_id = $request->get("kop_id");
        $toko_id = $request->get("toko_id");
        $sql = "
            SELECT * ,b.detailName nmKategory,c.detailName nmUnit ,d.nama_koperasi,'' nama_toko,e.detailName nmMerk
            from mst_barang a
            LEFT JOIN (SELECT * from mst_code_detail where masterCode='1000014'  ) b ON a.kategori=b.detailCode
            LEFT JOIN (SELECT * from mst_code_detail WHERE masterCode='1000013' and detailkop LIKE '%" . $kop_id . "%' ) c ON a.unit=c.detailCode
            LEFT JOIN mst_koperasi d ON a.kop_id=d.idx
            LEFT JOIN (SELECT * from mst_code_detail WHERE masterCode='1000016' and detailkop LIKE '%" . $kop_id . "%'  ) e ON a.merk=e.detailCode
            where    a.kop_id LIKE '%" . $kop_id . "%' and a.toko_id like  '%" . $toko_id . "%'
          ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selAnggota(Request $request, $cmd)
    {
        $kop_id =   $request->get("kop_id");
        $nama_anggota = '%' .  $request->get("nama_anggota") . '%';

        $sql = "
        SELECT *,b.nama_koperasi,c.detailName st_jenis_kelamin ,d.detailName st_pendidikan,e.detailName st_menikah,
                f.detailName st_agama,g.detailName st_pekerjaan,h.detailName2,
                case when ifnull(tanggal_masuk,'')='' then 'NOT_SET' ELSE case when DATEDIFF(date(NOW()),DATE(a.tanggal_masuk)) >h.detailName2 then 'OK' ELSE 'NOT_OK'  end  END sts
                from mst_anggota a
                LEFT JOIN mst_koperasi b ON a.no_koperasi=b.idx
                LEFT JOIN mst_code_detail c ON a.jenis_kelamin=c.detailCode AND c.masterCode='1000001'
                LEFT JOIN mst_code_detail d ON a.pendidikan=d.detailCode AND d.masterCode='1000002'
                LEFT JOIN mst_code_detail e ON a.status_perkawinan=e.detailCode AND e.masterCode='1000003'
                LEFT JOIN mst_code_detail f ON a.agama=f.detailCode AND f.masterCode='1000004'
                LEFT JOIN mst_code_detail g ON a.pekerjaan=g.detailCode AND g.masterCode='1000005'
                LEFT JOIN ( SELECT detailCode,detailName,detailName2
                            FROM mst_code_detail WHERE  masterCode='1000024' AND detailKop='" . $kop_id . "'
                            ORDER BY detailCode DESC  LIMIT 1 ) h ON 1=1
                WHERE b.idx= '" . $kop_id . "' and a.nama LIKE ?
                order by  nama
            ";
        $results = DB::select($sql, [$nama_anggota]);
        return json_encode($results);
    }

    public function selAnggotaTemp(Request $request, $cmd)
    {
        $kop_id =   $request->get("kop_id");
        $nama_anggota = '%' .  $request->get("nama_anggota") . '%';
        $sql = "
        SELECT *,b.nama_koperasi,c.detailName st_jenis_kelamin ,d.detailName st_pendidikan,e.detailName st_menikah,
                f.detailName st_agama,g.detailName st_pekerjaan,h.detailName2,
                case when ifnull(tanggal_masuk,'')='' then 'NOT_SET' ELSE case when DATEDIFF(date(NOW()),DATE(a.tanggal_masuk)) >h.detailName2 then 'OK' ELSE 'NOT_OK'  end  END sts
                from mst_anggota_temp a
                LEFT JOIN mst_koperasi b ON a.no_koperasi=b.idx
                LEFT JOIN mst_code_detail c ON a.jenis_kelamin=c.detailCode AND c.masterCode='1000001'
                LEFT JOIN mst_code_detail d ON a.pendidikan=d.detailCode AND d.masterCode='1000002'
                LEFT JOIN mst_code_detail e ON a.status_perkawinan=e.detailCode AND e.masterCode='1000003'
                LEFT JOIN mst_code_detail f ON a.agama=f.detailCode AND f.masterCode='1000004'
                LEFT JOIN mst_code_detail g ON a.pekerjaan=g.detailCode AND g.masterCode='1000005'
                LEFT JOIN ( SELECT detailCode,detailName,detailName2
                            FROM mst_code_detail WHERE  masterCode='1000024' AND detailKop='" . $kop_id . "'
                            ORDER BY detailCode DESC  LIMIT 1 ) h ON 1=1
                WHERE b.idx= '" . $kop_id . "' and a.nama LIKE ?
                order by  nama
            ";
        $results = DB::select($sql, [$nama_anggota]);
        return json_encode($results);
    }

    public function selLoginAnggota(Request $request, $cmd)
    {
        $userNm =  $request->get("userNm");
        $userPwd =  $request->get("userPwd");
        $sql = "
        SELECT *,b.nama_koperasi,c.detailName st_jenis_kelamin ,d.detailName st_pendidikan,e.detailName st_menikah,f.detailName st_agama,g.detailName st_pekerjaan
        from mst_anggota a
        LEFT JOIN mst_koperasi b ON a.no_koperasi=b.idx
        LEFT JOIN mst_code_detail c ON a.jenis_kelamin=c.detailCode AND c.masterCode='1000001'
        LEFT JOIN mst_code_detail d ON a.pendidikan=d.detailCode AND d.masterCode='1000002'
        LEFT JOIN mst_code_detail e ON a.status_perkawinan=e.detailCode AND e.masterCode='1000003'
        LEFT JOIN mst_code_detail f ON a.agama=f.detailCode AND f.masterCode='1000004'
        LEFT JOIN mst_code_detail g ON a.pekerjaan=g.detailCode AND g.masterCode='1000005'
        WHERE a.usernm=? AND a.pass=?
        order by  nama ";
        $results = DB::select($sql, [$userNm, $userPwd]);
        return json_encode($results);
    }

    public function selKoperasi(Request $request, $cmd)
    {
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $sql = "
        SELECT *,ifnull(b.agt,0) agt FROM
        mst_koperasi a
        LEFT  JOIN (SELECT no_koperasi , ifnull(COUNT(no_anggota),0) agt FROM mst_anggota WHERE no_koperasi LIKE ? GROUP BY no_koperasi ) b
        ON a.idx=b.no_koperasi
        where idx like ?
        ";
        $results = DB::select($sql, [$kop_id, $kop_id]);
        return json_encode($results);
    }

    public function selToko(Request $request, $cmd)
    {
        $toko_id = '%' .  $request->get("toko_id") . '%';
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $sql = "
        SELECT *,b.nama_koperasi,c.detailName AS strJenisToko FROM
        mst_toko a
        JOIN mst_koperasi b ON a.kop_id=b.idx
        JOIN mst_code_detail c ON a.jenis_toko=c.detailCode
        where toko_id LIKE ?  and kop_id like ? AND c.masterCode='1000021'
        ";
        $results = DB::select($sql, [$toko_id, $kop_id]);
        return json_encode($results);
    }

    public function selDashboardRegister(Request $request, $cmd)
    {
        $sql = "
        SELECT *,'Jumlah Koperasi' ket,COUNT(idx) jml,NOW() dt FROM mst_koperasi
        UNION ALL
        SELECT *,'Jumlah Anggota',COUNT(pktr) jml ,NOW() dt FROM mst_anggota_temp
        UNION ALL
        SELECT *,'Jumlah Anggota Pria',COUNT(pktr) jml,NOW() dt FROM mst_anggota_temp a  WHERE  a.jenis_kelamin='000001'
        UNION ALL
        SELECT *,'Jumlah Anggota Wanita',COUNT(pktr) jml,NOW() dt FROM mst_anggota_temp a  WHERE  a.jenis_kelamin='000002'
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selAllCodeAnggota(Request $request, $cmd)
    {
        $sql = "
        SELECT * FROM mst_code_detail  WHERE mastercode IN (
            '1000001','1000002','1000003','1000004','1000005'
            ) ORDER BY masterCode,detailIdx
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selAnggotaNomor(Request $request, $cmd)
    {
        $sql = "
            SELECT *,ifnull(CONCAT('AGT-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),CONCAT('AGT-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noAgt,
            ifnull(max(convert(REPLACE('','AGT-',''),DECIMAL))+1,1) agtNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selGudang(Request $request, $cmd)
    {
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $kode_gudang = '%' .  $request->get("kode_gudang") . '%';
        $sql = "
            SELECT * FROM mst_gudang WHERE kopId LIKE ?  and kode_gudang like ?
        ";
        $results = DB::select($sql, [$kop_id, $kode_gudang]);
        return json_encode($results);
    }
    public function selGudangNomor(Request $request, $cmd)
    {
        $sql = "
        SELECT *,ifnull(CONCAT('GDG-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),
        CONCAT('GDG-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noGudang,
        ifnull(max(convert(REPLACE('','GDG-',''),DECIMAL))+1,1) agtNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selPembeli(Request $request, $cmd)
    {
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $kode_pembeli = '%' .  $request->get("kode_pembeli") . '%';
        $sql = "
            SELECT * FROM mst_pembeli WHERE kop_id LIKE ?  and kode_pembeli like ?
        ";
        $results = DB::select($sql, [$kop_id, $kode_pembeli]);
        return json_encode($results);
    }
    public function selPembeliNomor(Request $request, $cmd)
    {
        $sql = "
        SELECT *,ifnull(CONCAT('PMB-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),
        CONCAT('PMB-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noPembeli,
        ifnull(max(convert(REPLACE('','PMB-',''),DECIMAL))+1,1) agtNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selSuplier(Request $request, $cmd)
    {
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $kode_suplier = '%' .  $request->get("kode_suplier") . '%';
        $sql = "
            SELECT * FROM mst_suplier WHERE kop_id LIKE ?  and kode_suplier like ?
        ";
        $results = DB::select($sql, [$kop_id, $kode_suplier]);
        return json_encode($results);
    }
    public function selSuplierNomor(Request $request, $cmd)
    {
        $sql = "
        SELECT *,ifnull(CONCAT('SPL-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),
        CONCAT('SPL-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noSuplier,
        ifnull(max(convert(REPLACE('','SPL-',''),DECIMAL))+1,1) agtNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selJenisSimpanan(Request $request, $cmd)
    {
        $sql = "
            SELECT * FROM mst_code_detail WHERE masterCode='1000015'
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selJenisTranKas(Request $request, $cmd)
    {
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $sql = "
            SELECT * FROM mst_code_detail WHERE masterCode='1000017' and detailKop like ?
        ";
        $results = DB::select($sql, [$kop_id]);
        return json_encode($results);
    }
    public function selBarcode(Request $request, $cmd)
    {
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $tgl_awal =  $request->get("tgl_awal");
        $tgl_akhir =  $request->get("tgl_akhir");
        $sql = "
        SELECT * FROM  mst_barang_rutin a
        join mst_barang b on a.kode_barang=b.idx
        WHERE a.kop_id like ? and date(a.tanggal_produksi) BETWEEN  ? AND ?
        ";
        $results = DB::select($sql, [$kop_id, $tgl_awal, $tgl_akhir]);
        return json_encode($results);
    }
    public function selBarcodeDetail(Request $request, $cmd)
    {
        $kop_id = '%' .  $request->get("kop_id") . '%';
        $barcode =  $request->get("barcode");
        $sql = "
            SELECT *,c.nama_barang,b.tanggal_produksi,b.tanggal_kadaluarsa,b.harga,b.packing_qty,a.* FROM  mst_barang_rutin_detail a
            JOIN mst_barang_rutin b ON a.kode_barang=b.kode_barang AND a.kop_id=b.kop_id AND a.barcode=b.barcode
            JOIN mst_barang c ON a.kop_id=c.kop_id AND a.kode_barang=c.idx
            WHERE a.kop_id like ? AND a.barcode=? ORDER BY a.sequence
        ";
        $results = DB::select($sql, [$kop_id, $barcode]);
        return json_encode($results);
    }
    public function selPriceFello(Request $request, $cmd)
    {
        $kop_id =   $request->get("kop_id");
        $sql = "
        SELECT * FROM mst_fello_multi_biller where kop_id='" . $kop_id . "'
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
}
