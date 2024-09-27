<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryKoperasiController extends Controller
{
    //
    public function run(Request $request)
    {
        $cmd = $request->get("cmd");
        if ($cmd == "selPengajuanPinjaman") {
            $result = $this->selPengajuanPinjaman($request, $cmd);
        } else if ($cmd == "selNomorSimpan") {
            $result = $this->selNomorSimpan($request, $cmd);
        } else if ($cmd == "selNomorTransaksi") {
            $result = $this->selNomorTransaksi($request, $cmd);
        } else if ($cmd == "selNomorPengajuan") {
            $result = $this->selNomorPengajuan($request, $cmd);
        } else if ($cmd == "selTrSimpan") {
            $result = $this->selTrSimpan($request, $cmd);
        } else if ($cmd == "selPengajuan") {
            $result = $this->selPengajuan($request, $cmd);
        } else if ($cmd == "selDefaultPengajuan") {
            $result = $this->selDefaultPengajuan($request, $cmd);
        } else if ($cmd == "selDefaultPengajuanPinjaman") {
            $result = $this->selDefaultPengajuanPinjaman($request, $cmd);
        } else if ($cmd == "selAngsuran") {
            $result = $this->selAngsuran($request, $cmd);
        } else if ($cmd == "selAngsuranDetail") {
            $result = $this->selAngsuranDetail($request, $cmd);
        } else if ($cmd == "selTrTran") {
            $result = $this->selTrTran($request, $cmd);
        } else if ($cmd == "selSaldoSimpanan") {
            $result = $this->selSaldoSimpanan($request, $cmd);
        } else if ($cmd == "selKasTransaksi") {
            $result = $this->selKasTransaksi($request, $cmd);
        } else if ($cmd == "selJurnalUmum") {
            $result = $this->selJurnalUmum($request, $cmd);
        } else if ($cmd == "selShu") {
            $result = $this->selShu($request, $cmd);
        } else if ($cmd == "selShuAnggota") {
            $result = $this->selShuAnggota($request, $cmd);
        } else if ($cmd == "selTranAssetHistory") {
            $result = $this->selTranAssetHistory($request, $cmd);
        } else if ($cmd == "selJurnalAsset") {
            $result = $this->selJurnalAsset($request, $cmd);
        } else if ($cmd == "selJurnalKas") {
            $result = $this->selJurnalKas($request, $cmd);
        } else if ($cmd == "selJurnalPos") {
            $result = $this->selJurnalPos($request, $cmd);
        } else if ($cmd == "selShuManual") {
            $result = $this->selShuManual($request, $cmd);
        } else if ($cmd == "selShuAnggotaManual") {
            $result = $this->selShuAnggotaManual($request, $cmd);
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

    public function selPengajuanPinjaman(Request $request, $cmd)
    {
        $no_anggota = $_POST["no_anggota"];
        $sql = "
            SELECT a.*,b.nama,c.detailName namaTipePinjaman,d.detailName lamaPinjaman,
            e.detailName stsPengajuan,f.nama_koperasi,DATE(tanggal_pengajuan) tglPengajuan ,DATE_FORMAT(a.status_dt, '%d %M  %Y') str_tgl_status,DATE_FORMAT(a.ins_dt, '%d %M  %Y') str_tgl_ins
            FROM
            tr_pengajuan_pinjaman a
            LEFT JOIN mst_anggota b ON a.no_anggota=b.no_anggota
            LEFT JOIN mst_code_detail c ON a.tipe_pinjaman=c.detailCode AND c.masterCode='1000011'
            LEFT JOIN mst_code_detail d ON a.lama_pinjaman_bulan=d.detailCode AND d.masterCode='1000010'
            LEFT JOIN mst_code_detail e ON a.`status`=e.detailCode AND e.masterCode='1000012'
            LEFT JOIN mst_koperasi f ON b.no_koperasi=f.idx
            where a.no_anggota='" . $no_anggota . "'
            order by tanggal_pengajuan

        ";
        $results = DB::select($sql);
        return json_encode($results);
    }


    public function selNomorSimpan(Request $request, $cmd)
    {
        $sql = "
        SELECT ifnull(CONCAT('TRSIM-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),CONCAT('TRSIM-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noTran,
        ifnull(max(convert(REPLACE('','TRSIM-',''),DECIMAL))+1,1) tranNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selNomorTransaksi(Request $request, $cmd)
    {
        $tipe = $_POST["tipe"];
        $sql = "
        SELECT ifnull(CONCAT('" . $tipe . "-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d-%H%m%s-%f'),1,22) ),CONCAT('" . $tipe . "-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d-%H%m%s-%f'),1,22) )) as noTran,
        ifnull(max(convert(REPLACE(noTran,'" . $tipe . "-',''),DECIMAL))+1,1) transNumber
        from tr_kas
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selNomorPengajuan(Request $request, $cmd)
    {
        $sql = "
        SELECT ifnull(CONCAT('TRPENG-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),CONCAT('TRPENG-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noTran,
        ifnull(max(convert(REPLACE('','TRPENG-',''),DECIMAL))+1,1) tranNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selTrSimpan(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id =  $_POST["kop_id"];
        $coa = $_POST["coa"] . '%';
        $sql = "
            SELECT a.*,b.detailName nama_detail_coa,b.detailName2 coaName,c.detailName jenisSimpan,'' coaDetail ,DATE(tanggal_simpan) tglSimpan,d.nama
            FROM tr_simpan a
            JOIN mst_code_detail b ON   a.coa=b.detailcode and b.masterCode='1000008' and b.detailKop='" . $kop_id . "'
            JOIN mst_code_detail c ON   a.jenisSimpan=c.detailcode AND c.masterCode='1000015'
            JOIN mst_anggota d ON a.noAgt=d.no_anggota AND d.no_koperasi ='" . $kop_id . "'
            WHERE  date(tanggal_simpan)  BETWEEN ? AND ?  and b.detailName2 like  ?   order by dateIns

        ";
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir, $coa]);
        return json_encode($results);
    }

    public function selTrTran(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id =  $_POST["kop_id"];
        $coa = $_POST["coa"] . '%';
        $sql = "
        SELECT a.*,b.detailName nama_detail_coa,b.detailName2 coaName ,c.detailName jenisSimpan,'' coaDetail ,DATE(tanggal_tran) tglSimpan
        FROM tr_kas a
        LEFT JOIN mst_code_detail b ON   a.coa=b.detailcode and b.masterCode='1000008' and b.detailKop='" . $kop_id . "'
        LEFT JOIN mst_code_detail c ON   a.jenisTran=c.detailcode AND c.masterCode='1000017' and c.detailKop='" . $kop_id . "'
        WHERE
        date(tanggal_tran) BETWEEN ? AND ?  and b.detailName2 like  ? order by tanggal_tran
        ";
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir, $coa]);
        return json_encode($results);
    }

    public function selPengajuan(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $sql = "
        SELECT a.*,b.nama,c.detailName namaTipePinjaman,d.detailName lamaPinjaman,e.detailName stsPengajuan,f.nama_koperasi,DATE(tanggal_pengajuan) tglPengajuan
        FROM
        tr_pengajuan_pinjaman a
        JOIN mst_anggota b ON a.no_anggota=b.no_anggota
        JOIN mst_code_detail c ON   a.tipe_pinjaman=c.detailCode AND c.masterCode='1000011' AND c.detailKop like '%" . $kop_id . "%'
        JOIN mst_code_detail d ON a.lama_pinjaman=d.detailCode AND d.masterCode='1000010' AND d.detailKop like '%" . $kop_id . "%'
        JOIN mst_code_detail e ON a.`status`=e.detailCode AND e.masterCode='1000012'
       JOIN mst_koperasi f ON b.no_koperasi=f.idx
        WHERE DATE(a.tanggal_pengajuan ) BETWEEN DATE(?) AND DATE(?) AND kop_id LIKE ?  order by b.nama,tanggal_pengajuan

        ";
        //var_dump($sql);
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir, $kop_id]);
        return json_encode($results);
    }

    public function selDefaultPengajuan(Request $request, $cmd)
    {
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $sql = "
            SELECT * FROM mst_code_detail WHERE masterCode IN ('1000010','1000011') AND detailKop like ? ORDER BY masterCode,detailIdx
        ";
        $results = DB::select($sql, [$kop_id]);
        return json_encode($results);
    }

    public function selDefaultPengajuanPinjaman(Request $request, $cmd)
    {
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $sql = "
        SELECT * FROM mst_code_detail WHERE masterCode IN ('1000011','1000010') and detailKop LIKE  ? ORDER BY mastercode,detailcode
        ";
        $results = DB::select($sql, [$kop_id]);
        return json_encode($results);
    }
    public function selAngsuran(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $noAnggota = '%' . $_POST["noAnggota"] . '%';
        $sql = "
        SELECT a.*,b.nama,c.nama_koperasi,d.jumlah_pinjaman,DATE(jatuh_tempo) str_jatuh_tempo,d.idx FROM
        tr_angsuran a
        LEFT JOIN mst_anggota b ON a.no_anggota=b.no_anggota
        LEFT JOIN mst_koperasi c ON a.kop_id=c.idx
        LEFT JOIN tr_pengajuan_pinjaman d ON a.no_pengajuan=d.noPengajuan
        where  DATE(a.insDt) BETWEEN DATE(?) AND DATE(?) AND a.kop_id LIKE ? and a.no_anggota like ?  order by no_pengajuan,angsuran_ke
        ";
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir, $kop_id, $noAnggota]);
        return json_encode($results);
    }

    public function selAngsuranDetail(Request $request, $cmd)
    {
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $noPengajuan =  $_POST["noPengajuan"];
        $sql = "
        SELECT a.*,b.nama,c.nama_koperasi,d.jumlah_pinjaman,DATE(jatuh_tempo) str_jatuh_tempo,d.idx FROM
        tr_angsuran a
        LEFT JOIN mst_anggota b ON a.no_anggota=b.no_anggota
        LEFT JOIN mst_koperasi c ON a.kop_id=c.idx
        LEFT JOIN tr_pengajuan_pinjaman d ON a.no_pengajuan=d.noPengajuan
        where a.kop_id LIKE ? and a.no_pengajuan= ?  order by no_pengajuan,angsuran_ke
        ";
        $results = DB::select($sql, [$kop_id, $noPengajuan]);
        return json_encode($results);
    }

    public function selSaldoSimpanan(Request $request, $cmd)
    {
        $kop_id =  $_POST["kop_id"];
        $no_agt =  $_POST["no_agt"];
        $jenisSimpan =  $_POST["jenisSimpan"];
        $sql = "

            SELECT ifnull(SUM(a.amount),0)-ifnull(SUM(b.amount),0) amount
            FROM ( select ifnull(SUM(amount),0) amount from tr_simpan a
            LEFT JOIN mst_code_detail b ON   a.coa=b.detailcode AND b.detailKop='" . $kop_id . "'
            WHERE appyn='APPROVE' AND noagt='" . $no_agt . "'
            AND b.detailName2='MASUK'  AND jenisSimpan='" . $jenisSimpan . "' ) a

            JOIN
            ( SELECT ifnull(SUM(amount),0) amount
            FROM tr_simpan a
            LEFT JOIN mst_code_detail b ON   a.coa=b.detailcode AND b.detailKop='" . $kop_id . "'
            WHERE appyn='APPROVE' AND noagt='" . $no_agt . "'
            AND b.detailName2='KELUAR'  AND jenisSimpan='" . $jenisSimpan . "' ) b ON 1=1
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selKasTransaksi(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id =  $_POST["kop_id"];
        $coa =  $_POST["coa"];
        $jenisTran = $_POST["jenisTran"];
        $sql = "
        SELECT a.*,DATE(tanggal_tran) tglTran,b.detailName coaName , b.detailName2 coaTipe,c.detailName jenisTransaksi
        FROM tr_kas a
        left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
        left JOIN mst_code_detail c ON a.jenisTran=c.detailCode AND c.masterCode='1000017' AND c.detailKop='" . $kop_id . "'
        WHERE  DATE(a.tanggal_tran) BETWEEN DATE(?) AND DATE(?) AND kop_id LIKE '%" . $kop_id . "%'  AND a.coa like '" . $coa . "%' AND a.jenisTran like '" . $jenisTran . "%'
        order by dateIns
        ";

        $results = DB::select($sql, [$tgl_awal, $tgl_akhir]);
        return json_encode($results);
    }
    public function selJurnalUmum(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id =  $_POST["kop_id"];
        $coa =  $_POST["coa"];
        $jenisTran = $_POST["jenisTran"];
        $sql = "
        SELECT noTran,
        idx,
        tanggal_tran,
        coa,
        kop_id,
        nama_transaksi,
        jenisTran,
        sum(amount) amount,
        appYn,
        verify,
        appDt,
        appUser,
        verifyDt,
        verifyUser,
        userIns,
        dateIns,
        tglTran,
        coaName,
        coaTipe,
        jenisTransaksi FROM (

                    SELECT 'KAS' TR,noTran,idx,tanggal_tran,coa,kop_id,nama_transaksi,jenisTran,amount,appYn,verify,date(appDt) appDt,appUser,date(verifyDt)verifyDt,
                    verifyUser,userIns,date(dateIns) dateIns,
                    DATE(tanggal_tran) tglTran,b.detailName coaName , b.detailName2 coaTipe,c.detailName jenisTransaksi
                    FROM tr_kas a
                    left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                    left JOIN mst_code_detail c ON a.jenisTran=c.detailCode AND c.masterCode='1000017' AND c.detailKop='" . $kop_id . "'
                    WHERE  DATE_FORMAT(a.tanggal_tran , '%Y-%m')='" . $tgl_awal . "'   AND kop_id LIKE '%" . $kop_id . "%'
                    AND a.jenisTran like '%' AND a.appYn='APPROVE'

                    UNION all

                    SELECT 'POSCORE' TR,CONCAT('AR-POS-',DATE_FORMAT(dateIns,'%Y%m%d')) noTran, CONCAT('AR-POS-',DATE_FORMAT(dateIns,'%Y%m%d')) idx,date(dateIns) tanggal_tran,'000001' coa,kop_id,
                    CONCAT('TRANSAKSI POS CORE ',DATE_FORMAT(dateIns,'%Y%m%d')) nama_transaksi,
                    '000001' jenisTran,amount,appYn,verify, null appDt,'' appUser, null verifyDt,'' verifyUser,userIns,date(dateIns) dateIns,
                    DATE(dateIns) tglTran,'TRANSAKSI POS' coaName , 'MASUK'  coaTipe,'TRANSAKSI POS CORE' jenisTransaksi
                    FROM tr_pos_master a
                    WHERE  DATE_FORMAT(a.dateIns , '%Y-%m')='" . $tgl_awal . "'  AND kop_id LIKE '%" . $kop_id . "%'

                    UNION all

                    SELECT 'SIMPIN' TR, case when b.detailName2='MASUK' then CONCAT('AR-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) ELSE CONCAT('AP-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) end noTran,
                    case when b.detailName2='MASUK' then CONCAT('AR-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) ELSE CONCAT('AP-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) end idx,
                    date(tanggal_simpan) tanggal_tran,coa,a1.no_koperasi,CONCAT('TRANSAKSI SIMPIN CORE ',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) nama_transaksi,
                    '' jenisTran,amount,appYn,verify, null appDt,'' appUser, null verifyDt,'' verifyUser,userIns,tanggal_simpan,DATE(a.tanggal_simpan) tglTran,b.detailName coaName ,
                    b.detailName2 coaTipe,'' jenisTransaksi
                    FROM tr_simpan a
                    JOIN mst_anggota a1 ON a.noAgt=a1.no_anggota
                    left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                    WHERE  DATE_FORMAT(a.tanggal_simpan , '%Y-%m')='" . $tgl_awal . "'  AND a1.no_koperasi LIKE '%" . $kop_id . "%'
                    AND a.appYn='APPROVE'


                    ) a GROUP BY   noTran,
                    idx,
                    tanggal_tran,
                    coa,
                    kop_id,
                    nama_transaksi,
                    jenisTran,
                    appYn,
                    verify,
                    appDt,
                    appUser,
                    verifyDt,
                    verifyUser,
                    userIns,
                    dateIns,
                    tglTran,
                    coaName,
                    coaTipe,
                    jenisTransaksi
                    order by tanggal_tran
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selShu(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id =  $_POST["kop_id"];
        $coa =  $_POST["coa"];
        $jenisTran = $_POST["jenisTran"];
        $sql = "
        SELECT coaTipe,SUM(amount) amount  FROM (
            SELECT 'KAS' TR,noTran,idx,tanggal_tran,coa,kop_id,nama_transaksi,jenisTran,amount,appYn,verify,date(appDt) appDt,appUser,date(verifyDt)verifyDt,
            verifyUser,userIns,date(dateIns) dateIns,
            DATE(tanggal_tran) tglTran,b.detailName coaName , b.detailName2 coaTipe,c.detailName jenisTransaksi
            FROM tr_kas a
            left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
            left JOIN mst_code_detail c ON a.jenisTran=c.detailCode AND c.masterCode='1000017' AND c.detailKop='" . $kop_id . "'
            WHERE  DATE_FORMAT(a.tanggal_tran , '%Y-%m')='" . $tgl_awal . "'   AND kop_id LIKE '%" . $kop_id . "%'
            AND a.jenisTran like '%' AND a.appYn='APPROVE'  AND c.detailName NOT LIKE '%ASSET%' and c.detailName NOT LIKE '%DANA CADANGAN%' and c.detailName NOT LIKE '%BALANCE%'

            UNION all

            SELECT 'POSCORE' TR,CONCAT('AR-POS-',DATE_FORMAT(dateIns,'%Y%m%d')) noTran, CONCAT('AR-POS-',DATE_FORMAT(dateIns,'%Y%m%d')) idx,date(dateIns) tanggal_tran,'000001' coa,kop_id,
            CONCAT('TRANSAKSI POS CORE ',DATE_FORMAT(dateIns,'%Y%m%d')) nama_transaksi,
            '000001' jenisTran,amount,appYn,verify, null appDt,'' appUser, null verifyDt,'' verifyUser,userIns,date(dateIns) dateIns,
            DATE(dateIns) tglTran,'TRANSAKSI POS' coaName , 'MASUK'  coaTipe,'TRANSAKSI POS CORE' jenisTransaksi
            FROM tr_pos_master a
            WHERE  DATE_FORMAT(a.dateIns , '%Y-%m')='" . $tgl_awal . "'  AND kop_id LIKE '%" . $kop_id . "%'

            UNION all

            SELECT 'SIMPIN' TR, case when b.detailName2='MASUK' then CONCAT('AR-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) ELSE CONCAT('AP-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) end noTran,
            case when b.detailName2='MASUK' then CONCAT('AR-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) ELSE CONCAT('AP-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) end idx,
            date(tanggal_simpan) tanggal_tran,coa,a1.no_koperasi,CONCAT('TRANSAKSI SIMPIN CORE ',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) nama_transaksi,
            '' jenisTran,amount,appYn,verify, null appDt,'' appUser, null verifyDt,'' verifyUser,userIns,tanggal_simpan,DATE(a.tanggal_simpan) tglTran,b.detailName coaName ,
            b.detailName2 coaTipe,'' jenisTransaksi
            FROM tr_simpan a
            JOIN mst_anggota a1 ON a.noAgt=a1.no_anggota
            left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
            WHERE  DATE_FORMAT(a.tanggal_simpan , '%Y-%m')='" . $tgl_awal . "'   AND a1.no_koperasi LIKE '%" . $kop_id . "%'
            AND a.appYn='APPROVE' ) a
            GROUP BY coaTipe order by coaTipe desc
        ";

        $results = DB::select($sql);
        $sqlResult = "";
        $bal = 0;
        if (count($results) > 0) {
            for ($x = 0; $x < count($results); $x++) {
                if ($results[$x]->coaTipe == "MASUK") {
                    $sqlResult = $sqlResult . "select 'DANA MASUK' tipe,'' shu, '' val," . $results[$x]->amount . " amount";
                    $bal = $bal + $results[$x]->amount;
                } else {
                    if (count($results) > 1) {
                        $sqlResult = $sqlResult . "  union all ";
                    }
                    $sqlResult = $sqlResult . "select 'DANA KELUAR' tipe,'' shu,'' val, " . $results[$x]->amount . " amount";
                    $bal = $bal - $results[$x]->amount;
                }
            }

            $sqlResult = $sqlResult . " union all select 'TOTAL PENDAPATAN' tipe,'' shu,'' val, " . $bal . " amount";
            $sql = " SELECT * FROM mst_code_detail WHERE masterCode='1000023' AND detailKop='" . $kop_id . "' ORDER BY detailCode ";
            $resultsShuItem = DB::select($sql);
            $shuAmount = 0;
            if (count($resultsShuItem) > 0) {
                //var_dump($resultsShuItem);
                for ($u = 0; $u < count($resultsShuItem); $u++) {
                    if ($resultsShuItem[$u]->detailName2 == "DANA_CADANGAN") {
                        $sqlResult = $sqlResult . " union all ";
                        $sqlResult = $sqlResult . " select 'DANA CADANGAN' tipe,'" . $resultsShuItem[$u]->detailName2 . "' shu,'" . $resultsShuItem[$u]->detailDesc . "' val, (" . $bal . "/100*" . $resultsShuItem[$u]->detailDesc . ") amount";
                        $shuAmount = $bal - ($bal / 100 * $resultsShuItem[$u]->detailDesc);
                        $sqlResult = $sqlResult . " union all select 'DANA PEMBAGIAN SHU' tipe,'' shu,'" . (100 - $resultsShuItem[$u]->detailDesc) . "' val, " . $shuAmount . " amount";
                    } else {
                        $sqlResult = $sqlResult . " union all ";
                        $sqlResult = $sqlResult . " select '" . $resultsShuItem[$u]->detailName . "' tipe, '" . $resultsShuItem[$u]->detailName2 . "' shu,'" . $resultsShuItem[$u]->detailDesc . "' val,(" . $shuAmount . "/100*" . $resultsShuItem[$u]->detailDesc . ") amount";
                    }
                }
            }
            $results1 = DB::select($sqlResult);
        } else {
            $sqlResult = $sqlResult . " select 'No Data' tipe,'' shu,'' val, 0 amount";
            $results1 = DB::select($sqlResult);
        }


        return json_encode($results1);
    }

    public function selShuAnggota(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id =  $_POST["kop_id"];
        $jasa_usaha =  $_POST["jasa_usaha"];
        $jasa_modal = $_POST["jasa_modal"];
        $sql = "
        SELECT a.no_anggota,a.no_anggota_internal,a.nama,a.no_koperasi,
        ifnull(b.danaSimpan,0) danaSimpan,ifnull(c.danaKeluar,0) danaKeluar,ifnull(b.danaSimpan,0)-ifnull(c.danaKeluar,0) balAnggota,
        ifnull(d.danaTotalMasuk,0) danaTotalMasuk,ifnull(e.danaTotalKeluar,0)danaTotalKeluar,ifnull(d.danaTotalMasuk,0)-ifnull(e.danaTotalKeluar,0) balKoperasi,
        (ifnull(b.danaSimpan,0)-ifnull(c.danaKeluar,0))/(ifnull(d.danaTotalMasuk,0)-ifnull(e.danaTotalKeluar,0))*" . $jasa_modal . " jma,
        (ifnull(b.danaSimpan,0)-ifnull(c.danaKeluar,0))/(ifnull(d.danaTotalMasuk,0)-ifnull(e.danaTotalKeluar,0))*" . $jasa_usaha . " jua,
        ((ifnull(b.danaSimpan,0)-ifnull(c.danaKeluar,0))/(ifnull(d.danaTotalMasuk,0)-ifnull(e.danaTotalKeluar,0))*" . $jasa_modal . ")+
        ((ifnull(b.danaSimpan,0)-ifnull(c.danaKeluar,0))/(ifnull(d.danaTotalMasuk,0)-ifnull(e.danaTotalKeluar,0))*" . $jasa_usaha . ") shu
        FROM mst_anggota a
        LEFT JOIN (
                SELECT a.noAgt,a1.no_koperasi,
                case when b.detailName2='MASUK' then ifnull(SUM(amount),0) END danaSimpan
                FROM tr_simpan a
                JOIN mst_anggota a1 ON a.noAgt=a1.no_anggota
                left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                WHERE   DATE(a.tanggal_simpan) BETWEEN DATE('" . $tgl_awal . "') AND DATE('" . $tgl_akhir . "') AND
                a1.no_koperasi LIKE '%" . $kop_id . "%' AND
                a.appYn='APPROVE' AND b.detailName2='MASUK'
                GROUP BY a.noAgt,a1.no_koperasi,b.detailName2
            ) b ON a.no_anggota=b.noAgt AND a.no_koperasi=b.no_koperasi
        LEFT JOIN (
                SELECT a.noAgt,a1.no_koperasi,
                case when b.detailName2='KELUAR' then ifnull(SUM(amount),0) END danaKeluar
                FROM tr_simpan a
                JOIN mst_anggota a1 ON a.noAgt=a1.no_anggota
                left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                WHERE   DATE(a.tanggal_simpan) BETWEEN DATE('" . $tgl_awal . "') AND DATE('" . $tgl_akhir . "') AND
                a1.no_koperasi LIKE '%" . $kop_id . "%' AND
                a.appYn='APPROVE' AND b.detailName2='KELUAR'
                GROUP BY a.noAgt,a1.no_koperasi,b.detailName2
            ) c ON a.no_anggota=c.noAgt AND a.no_koperasi=c.no_koperasi
        LEFT JOIN (
                SELECT a1.no_koperasi,
                case when b.detailName2='MASUK' then ifnull(SUM(amount),0) END danaTotalMasuk
                FROM tr_simpan a
                JOIN mst_anggota a1 ON a.noAgt=a1.no_anggota
                left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                WHERE   DATE(a.tanggal_simpan) BETWEEN DATE('" . $tgl_awal . "') AND DATE('" . $tgl_akhir . "') AND
                a1.no_koperasi LIKE '%" . $kop_id . "%' AND
                a.appYn='APPROVE' AND b.detailName2='MASUK'
                GROUP BY a1.no_koperasi,b.detailName2
            ) d ON  a.no_koperasi=d.no_koperasi
        LEFT JOIN (
                SELECT a1.no_koperasi,
                case when b.detailName2='KELUAR' then ifnull(SUM(amount),0) END danaTotalKeluar
                FROM tr_simpan a
                JOIN mst_anggota a1 ON a.noAgt=a1.no_anggota
                left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                WHERE  DATE(a.tanggal_simpan) BETWEEN DATE('" . $tgl_awal . "') AND DATE('" . $tgl_akhir . "') AND
                a1.no_koperasi LIKE '%" . $kop_id . "%' AND
                a.appYn='APPROVE' AND b.detailName2='KELUAR'
                GROUP BY a1.no_koperasi,b.detailName2
            ) e ON  a.no_koperasi=e.no_koperasi
        WHERE a.no_koperasi='" . $kop_id . "'
        ";

        $results1 = DB::select($sql);
        return json_encode($results1);
    }
    public function selTranAssetHistory(Request $request, $cmd)
    {
        $kop_id =  $_POST["kop_id"];
        $namaAsset = $_POST["namaAsset"];
        $sql = "  SELECT a.* FROM tr_kas a
        JOIN mst_code_detail b ON a.coa=b.detailCode AND b.detailKop='" . $kop_id . "' AND b.masterCode='1000008'
        WHERE b.detailName LIKE '%Asset%' AND appYn='APPROVE' and a.nama_transaksi like '%" . $namaAsset . "%'
        ORDER BY a.tanggal_tran
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selJurnalAsset(Request $request, $cmd)
    {
        $kop_id =  $_POST["kop_id"];
        $namaAsset = $_POST["namaAsset"];
        $sql = "
            SELECT a.*,ifnull(c.amount,0) Kredit,b.detailName2 coaTipe,date(a.tanggal_tran) tglTran,ifnull(date(c.tanggal_tran),'') tglTranKredit
            FROM tr_kas a
            JOIN mst_code_detail b ON a.coa=b.detailCode AND b.detailKop='" . $kop_id . "' AND b.masterCode='1000008'
            LEFT JOIN (
            SELECT a.*
            FROM tr_kas a
            JOIN mst_code_detail b ON a.coa=b.detailCode AND b.detailKop='" . $kop_id . "' AND b.masterCode='1000008'
            WHERE b.detailName LIKE '%Asset%' AND appYn='APPROVE'
            ) c ON a.noTran=c.noTranContra
            WHERE b.detailName LIKE '%Asset%' AND a.appYn='APPROVE' AND b.detailName2 LIKE '%MASUK%'  AND a.nama_transaksi LIKE '%" . $namaAsset . "%'
            ORDER BY a.tanggal_tran

        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selJurnalKas(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $kop_id =  $_POST["kop_id"];
        $coa =  $_POST["coa"];
        $jenisTran = $_POST["jenisTran"];
        $sql = "
        SELECT noTran,
        idx,
        tanggal_tran,
        coa,
        kop_id,
        nama_transaksi,
        jenisTran,
        sum(amount) amount,
        appYn,
        verify,
        appDt,
        appUser,
        verifyDt,
        verifyUser,
        userIns,
        dateIns,
        tglTran,
        coaName,
        coaTipe,
        jenisTransaksi FROM (

                    SELECT 'KAS' TR,noTran,idx,tanggal_tran,coa,kop_id,nama_transaksi,jenisTran,amount,appYn,verify,date(appDt) appDt,appUser,date(verifyDt)verifyDt,
                    verifyUser,userIns,date(dateIns) dateIns,
                    DATE(tanggal_tran) tglTran,b.detailName coaName , b.detailName2 coaTipe,c.detailName jenisTransaksi
                    FROM tr_kas a
                    left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                    left JOIN mst_code_detail c ON a.jenisTran=c.detailCode AND c.masterCode='1000017' AND c.detailKop='" . $kop_id . "'
                    WHERE DATE_FORMAT(a.tanggal_tran , '%Y-%m')='" . $tgl_awal . "'    AND kop_id LIKE '%" . $kop_id . "%'
                    AND a.jenisTran like '%' AND a.appYn='APPROVE' and  b.detailName not LIKE '%Asset%'

                    UNION all

                    SELECT 'SIMPIN' TR, case when b.detailName2='MASUK' then CONCAT('AR-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) ELSE CONCAT('AP-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) end noTran,
                    case when b.detailName2='MASUK' then CONCAT('AR-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) ELSE CONCAT('AP-SIMPIN-',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) end idx,
                    date(tanggal_simpan) tanggal_tran,coa,a1.no_koperasi,CONCAT('TRANSAKSI SIMPIN CORE ',DATE_FORMAT(tanggal_simpan,'%Y%m%d')) nama_transaksi,
                    '' jenisTran,amount,appYn,verify, null appDt,'' appUser, null verifyDt,'' verifyUser,userIns,tanggal_simpan,DATE(a.tanggal_simpan) tglTran,b.detailName coaName ,
                    b.detailName2 coaTipe,'' jenisTransaksi
                    FROM tr_simpan a
                    JOIN mst_anggota a1 ON a.noAgt=a1.no_anggota
                    left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                    WHERE  DATE_FORMAT(a.tanggal_simpan , '%Y-%m')='" . $tgl_awal . "'  AND a1.no_koperasi LIKE '%" . $kop_id . "%'
                    AND a.appYn='APPROVE'


                    ) a GROUP BY   noTran,
                    idx,
                    tanggal_tran,
                    coa,
                    kop_id,
                    nama_transaksi,
                    jenisTran,
                    appYn,
                    verify,
                    appDt,
                    appUser,
                    verifyDt,
                    verifyUser,
                    userIns,
                    dateIns,
                    tglTran,
                    coaName,
                    coaTipe,
                    jenisTransaksi
                    order by tanggal_tran
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selJurnalPos(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id =  $_POST["kop_id"];
        $coa =  $_POST["coa"];
        $jenisTran = $_POST["jenisTran"];
        $sql = "
        SELECT noTran,
        idx,
        tanggal_tran,
        coa,
        kop_id,
        nama_transaksi,
        jenisTran,
        sum(amount) amount,
        appYn,
        verify,
        appDt,
        appUser,
        verifyDt,
        verifyUser,
        userIns,
        dateIns,
        tglTran,
        coaName,
        coaTipe,
        jenisTransaksi FROM (

                    SELECT 'POSCORE' TR,CONCAT('AR-POS-',DATE_FORMAT(dateIns,'%Y%m%d')) noTran, CONCAT('AR-POS-',DATE_FORMAT(dateIns,'%Y%m%d')) idx,date(dateIns) tanggal_tran,'000001' coa,kop_id,
                    CONCAT('TRANSAKSI POS CORE ',DATE_FORMAT(dateIns,'%Y%m%d')) nama_transaksi,
                    '000001' jenisTran,amount,appYn,verify, null appDt,'' appUser, null verifyDt,'' verifyUser,userIns,date(dateIns) dateIns,
                    DATE(dateIns) tglTran,'TRANSAKSI POS' coaName , 'MASUK'  coaTipe,'TRANSAKSI POS CORE' jenisTransaksi
                    FROM tr_pos_master a
                    WHERE   DATE_FORMAT(dateins , '%Y-%m')='" . $tgl_awal . "'  AND kop_id LIKE '%" . $kop_id . "%'


                    ) a GROUP BY   noTran,
                    idx,
                    tanggal_tran,
                    coa,
                    kop_id,
                    nama_transaksi,
                    jenisTran,
                    appYn,
                    verify,
                    appDt,
                    appUser,
                    verifyDt,
                    verifyUser,
                    userIns,
                    dateIns,
                    tglTran,
                    coaName,
                    coaTipe,
                    jenisTransaksi
                    order by tanggal_tran
        ";
        //print $sql;
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selShuManual(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id =  $_POST["kop_id"];
        $coa =  $_POST["coa"];
        $jenisTran = $_POST["jenisTran"];
        $sql = " ";
        $sqlResult = "";

        $sqlResult = $sqlResult . "select 'DANA MASUK' tipe,'' shu, 'dana_masuk' val,0 amount";
        $sqlResult = $sqlResult . "  union all ";
        $sqlResult = $sqlResult . "select 'DANA KELUAR' tipe,'' shu,'dana_keluar' val, 0 amount";

        $sqlResult = $sqlResult . " union all select 'TOTAL PENDAPATAN' tipe,'' shu,'total_pendapatan' val, 0 amount";
        $sql = " SELECT * FROM mst_code_detail WHERE masterCode='1000023' AND detailKop='" . $kop_id . "' ORDER BY detailCode ";
        $resultsShuItem = DB::select($sql);
        $shuAmount = 0;
        if (count($resultsShuItem) > 0) {
            //var_dump($resultsShuItem);
            for ($u = 0; $u < count($resultsShuItem); $u++) {
                if ($resultsShuItem[$u]->detailName2 == "DANA_CADANGAN") {
                    $sqlResult = $sqlResult . " union all ";
                    $sqlResult = $sqlResult . " select 'DANA CADANGAN' tipe,'" . $resultsShuItem[$u]->detailName2 . "' shu,'" . $resultsShuItem[$u]->detailDesc . "' val, 0 amount";
                    $sqlResult = $sqlResult . " union all select 'DANA PEMBAGIAN SHU' tipe,'' shu,'" . (100 - $resultsShuItem[$u]->detailDesc) . "' val,0 amount";
                } else {
                    $sqlResult = $sqlResult . " union all ";
                    $sqlResult = $sqlResult . " select '" . $resultsShuItem[$u]->detailName . "' tipe, '" . $resultsShuItem[$u]->detailName2 . "' shu,'" . $resultsShuItem[$u]->detailDesc . "' val,0 amount";
                }
            }
        }
        //echo $sqlResult;
        $results1 = DB::select($sqlResult);
        return json_encode($results1);
    }
    public function selShuAnggotaManual(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id =  $_POST["kop_id"];
        $jasa_usaha =  $_POST["jasa_usaha"];
        $jasa_modal = $_POST["jasa_modal"];
        $sql = "
        SELECT a.no_anggota,a.no_anggota_internal,a.nama,a.no_koperasi,
        ifnull(b.danaSimpan,0) danaSimpan,ifnull(c.danaKeluar,0) danaKeluar,ifnull(b.danaSimpan,0)-ifnull(c.danaKeluar,0) balAnggota,
        ifnull(d.danaTotalMasuk,0) danaTotalMasuk,ifnull(e.danaTotalKeluar,0)danaTotalKeluar,ifnull(d.danaTotalMasuk,0)-ifnull(e.danaTotalKeluar,0) balKoperasi,
        0 jma,
        0 jua,
        0 shu
        FROM mst_anggota a
        LEFT JOIN (
                SELECT a.noAgt,a1.no_koperasi,
                case when b.detailName2='MASUK' then ifnull(SUM(amount),0) END danaSimpan
                FROM tr_simpan a
                JOIN mst_anggota a1 ON a.noAgt=a1.no_anggota
                left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                WHERE
                a1.no_koperasi LIKE '%" . $kop_id . "%' AND
                a.appYn='APPROVE' AND b.detailName2='MASUK'
                GROUP BY a.noAgt,a1.no_koperasi,b.detailName2
            ) b ON a.no_anggota=b.noAgt AND a.no_koperasi=b.no_koperasi
        LEFT JOIN (
                SELECT a.noAgt,a1.no_koperasi,
                case when b.detailName2='KELUAR' then ifnull(SUM(amount),0) END danaKeluar
                FROM tr_simpan a
                JOIN mst_anggota a1 ON a.noAgt=a1.no_anggota
                left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                WHERE
                a1.no_koperasi LIKE '%" . $kop_id . "%' AND
                a.appYn='APPROVE' AND b.detailName2='KELUAR'
                GROUP BY a.noAgt,a1.no_koperasi,b.detailName2
            ) c ON a.no_anggota=c.noAgt AND a.no_koperasi=c.no_koperasi
        LEFT JOIN (
                SELECT a1.no_koperasi,
                case when b.detailName2='MASUK' then ifnull(SUM(amount),0) END danaTotalMasuk
                FROM tr_simpan a
                JOIN mst_anggota a1 ON a.noAgt=a1.no_anggota
                left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                WHERE
                a1.no_koperasi LIKE '%" . $kop_id . "%' AND
                a.appYn='APPROVE' AND b.detailName2='MASUK'
                GROUP BY a1.no_koperasi,b.detailName2
            ) d ON  a.no_koperasi=d.no_koperasi
        LEFT JOIN (
                SELECT a1.no_koperasi,
                case when b.detailName2='KELUAR' then ifnull(SUM(amount),0) END danaTotalKeluar
                FROM tr_simpan a
                JOIN mst_anggota a1 ON a.noAgt=a1.no_anggota
                left JOIN mst_code_detail b ON a.coa=b.detailCode AND b.masterCode='1000008' AND b.detailKop='" . $kop_id . "'
                WHERE
                a1.no_koperasi LIKE '%" . $kop_id . "%' AND
                a.appYn='APPROVE' AND b.detailName2='KELUAR'
                GROUP BY a1.no_koperasi,b.detailName2
            ) e ON  a.no_koperasi=e.no_koperasi
        WHERE a.no_koperasi='" . $kop_id . "'
        ";

        $results1 = DB::select($sql);
        return json_encode($results1);
    }
}
