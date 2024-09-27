<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryPosController extends Controller
{
    //
    public function run(Request $request)
    {
        $cmd = $request->get("cmd");
        if ($cmd == "selDataBarang") {
            $result = $this->selDataBarang($request, $cmd);
        } else if ($cmd == "selDataBarangInventory") {
            $result = $this->selDataBarangInventory($request, $cmd);
        } else if ($cmd == "selPosResult") {
            $result = $this->selPosResult($request, $cmd);
        } else if ($cmd == "selPosResultMobile") {
            $result = $this->selPosResultMobile($request, $cmd);
        } else if ($cmd == "selAllItem") {
            $result = $this->selAllItem($request, $cmd);
        } else if ($cmd == "selPpobResult") {
            $result = $this->selPpobResult($request, $cmd);
        } else if ($cmd == "selPpobMobileResult") {
            $result = $this->selPpobMobileResult($request, $cmd);
        } else if ($cmd == "selClosing") {
            $result = $this->selClosing($request, $cmd);
        } else if ($cmd == "selClosingByDate") {
            $result = $this->selClosingByDate($request, $cmd);
        } else if ($cmd == "selPosMobileCore") {
            $result = $this->selPosMobileCore($request, $cmd);
        } else if ($cmd == "selPosDefaultSetting") {
            $result = $this->selPosDefaultSetting($request, $cmd);
        } else if ($cmd == "selPosBarcode") {
            $result = $this->selPosBarcode($request, $cmd);
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

    public function selDataBarang(Request $request, $cmd)
    {
        $barcode = '%' . $_POST["barcode"] . '%';
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $sql = "
            SELECT *,b.nama_koperasi FROM mst_barang a
            LEFT JOIN mst_koperasi b ON a.kop_id=b.idx
            WHERE barcode_kemasan like ? and kop_id like ? order by kategori,nama_barang
        ";
        $results = DB::select($sql, [$barcode, $kop_id]);
        return json_encode($results);
    }

    public function selPosDefaultSetting(Request $request, $cmd)
    {
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $sql = "
        SELECT * FROM mst_code_detail WHERE masterCode='1000022' AND detailKop like ?
        ";
        $results = DB::select($sql, [$kop_id]);
        return json_encode($results);
    }

    public function selDataBarangInventory(Request $request, $cmd)
    {
        $date = $_POST["date"] . "-01";
        $kop_id =  $_POST["kop_id"];
        $sql = "
        SELECT *,b.nama_koperasi,
        case when a.min_stock=0 then 0 else stock+ ifnull(h.qtyGrn,0)-ifnull(f.qtyPos,0)-ifnull(g.qtyDo,0) end beginQty,
		  ifnull(d.qtyDo,0) outQtyDo,ifnull(c.qtyPos,0) outQtyPos,ifnull(e.qtyGrn,0) inQty,
		  case when a.min_stock=0 then 0 else
          stock+ ifnull(h.qtyGrn,0)-ifnull(f.qtyPos,0)-ifnull(g.qtyDo,0) +( ifnull(e.qtyGrn,0)-ifnull(c.qtyPos,0)-ifnull(d.qtyDo,0) )
          end endQty
        FROM mst_barang a
         LEFT JOIN mst_koperasi b ON a.kop_id=b.idx
         LEFT JOIN (  select kode_barang,SUM(qty) qtyPos
            from tr_pos_detail where DATE_FORMAT(dateIns,'%Y%m') BETWEEN DATE_FORMAT('" . $date . "','%Y%m')  AND DATE_FORMAT('" . $date . "','%Y%m')
            and kop_id='" . $kop_id . "'
            GROUP BY kode_barang ) c ON a.idx=c.kode_barang
         LEFT JOIN (  select kode_barang,SUM(jumlah_barang) qtyDo
            from tr_do_detail where DATE_FORMAT(dateIns,'%Y%m') BETWEEN DATE_FORMAT('" . $date . "','%Y%m')  AND DATE_FORMAT('" . $date . "','%Y%m')
            and kop_id='" . $kop_id . "'
            GROUP BY kode_barang ) d ON a.idx=d.kode_barang
         LEFT JOIN (  select kode_barang,SUM(jumlah_barang) qtyGrn
            from tr_grn_detail where DATE_FORMAT(dateIns,'%Y%m') BETWEEN DATE_FORMAT('" . $date . "','%Y%m')  AND DATE_FORMAT('" . $date . "','%Y%m')
            and kop_id='" . $kop_id . "'
            GROUP BY kode_barang ) e ON a.idx=e.kode_barang

         LEFT JOIN (  select kode_barang,SUM(qty) qtyPos
            from tr_pos_detail where DATE_FORMAT(dateIns,'%Y%m') BETWEEN '202301' AND DATE_FORMAT(DATE_ADD('" . $date . "', INTERVAL -1 month),'%Y%m')
            and kop_id='" . $kop_id . "'
            GROUP BY kode_barang ) f ON a.idx=f.kode_barang
         LEFT JOIN (  select kode_barang,SUM(jumlah_barang) qtyDo
            from tr_do_detail where DATE_FORMAT(dateIns,'%Y%m') BETWEEN '202301' AND DATE_FORMAT(DATE_ADD('" . $date . "', INTERVAL -1 month),'%Y%m')
            and kop_id='" . $kop_id . "'
            GROUP BY kode_barang ) g ON a.idx=g.kode_barang
         LEFT JOIN (  select kode_barang,SUM(jumlah_barang) qtyGrn
            from tr_grn_detail where DATE_FORMAT(dateIns,'%Y%m') BETWEEN '202301' AND DATE_FORMAT(DATE_ADD('" . $date . "', INTERVAL -1 month),'%Y%m')
            and kop_id='" . $kop_id . "'
            GROUP BY kode_barang ) h ON a.idx=h.kode_barang
         WHERE a.inventoryYn='Y'
         and  kop_id like '%" . $kop_id . "%' order by kategori,nama_barang
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selPosResult(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id = $_POST["kop_id"];
        $toko_id =  $_POST["toko_id"];
        $tipe_bayar =  $_POST["tipeBayar"];
        $sql = "
            SELECT a.*,case when ifnull(c.nama,'')='' then ifnull(d.namaUser,'') ELSE ifnull(c.nama,'') END nama,b.nama_koperasi,DATE(a.dateins) transactionDate,e.detailName strTipebayar
            FROM tr_pos_master a
            JOIN mst_koperasi b ON a.kop_id=b.idx
            LEFT JOIN mst_anggota c ON a.no_anggota=c.no_anggota AND c.no_koperasi='" . $kop_id . "'
            LEFT JOIN mst_user d ON a.no_anggota=d.userId AND d.no_koperasi='" . $kop_id . "'
            LEFT JOIN mst_code_detail e ON e.masterCode='1000018'   and a.tipe_bayar=e.detailcode
            LEFT JOIN mst_toko f ON a.kop_id=f.kop_id AND f.kop_id='" . $kop_id . "' AND f.toko_id LIKE '%" . $toko_id . "%'
            where  DATE(a.dateins) BETWEEN DATE(?) AND DATE(?) AND a.kop_id LIKE '%" . $kop_id . "%'  and sts='SUCCESS' and tipe_bayar like '%" . $tipe_bayar . "%'
            order by nama,dateins desc
        ";
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir]);
        return json_encode($results);
    }

    public function selPosResultMobile(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id = $_POST["kop_id"];
        $toko_id =  $_POST["toko_id"];
        $tipe_bayar =  $_POST["tipeBayar"];
        $sql = "
        SELECT
            a.*,
            CASE
                WHEN IFNULL(c.nama, '') = '' THEN IFNULL(d.namaUser, '')
                ELSE IFNULL(c.nama, '')
            END nama,
            b.nama_koperasi,
            DATE(a.dateins) AS transactionDate,
            e.detailName AS strTipebayar
        FROM tr_pos_mobile_master a
        JOIN mst_koperasi b ON a.kop_id = b.idx
        LEFT JOIN mst_anggota c ON a.no_anggota = c.no_anggota AND c.no_koperasi = ?
        LEFT JOIN mst_user d ON a.no_anggota = d.userId AND d.no_koperasi = ?
        LEFT JOIN mst_code_detail e ON e.masterCode = '1000018' AND a.tipe_bayar = e.detailcode
        LEFT JOIN mst_toko f ON a.kop_id = f.kop_id AND f.kop_id = ? AND f.toko_id LIKE ?
        WHERE DATE(a.dateins) BETWEEN DATE(?) AND DATE(?)
            AND a.kop_id LIKE ?
            AND sts = 'SUCCESS'
            AND a.tipe_bayar LIKE ?
        ORDER BY nama, a.dateins DESC
    ";

    // Menjalankan query dengan parameter binding
    $results = DB::select($sql, [
        $kop_id,
        $kop_id,
        $kop_id,
        '%'.$toko_id.'%',
        $tgl_awal,
        $tgl_akhir,
        '%'.$kop_id.'%',
        '%'.$tipe_bayar.'%'
    ]);
        return json_encode($results);
    }

    public function selPpobResult(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $sql = "
        SELECT *,DATE(dateIns) tglTran ,
        case when tipe_bayar='000001' then 'CASH' when tipe_bayar='000003' then 'FELLO_BALLANCE' ELSE 'QRIS' end tipeBayarNm,
        case when ifnull(product_name,'')='' then productCode ELSE biller_name END BillerName,
        case when ifnull(product_name,'')='' then productCode ELSE product_name END ProductName
        FROM tr_ppob_master a
        LEFT JOIN mst_fello_multi_biller b ON a.productCode=b.concat_id
        WHERE   DATE(dateIns) BETWEEN DATE(?) AND DATE(?) AND a.kop_id LIKE ?  order by dateIns desc
        ";
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir, $kop_id]);
        return json_encode($results);
    }

    public function selPpobMobileResult(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $sql = "
        SELECT *,DATE(dateIns) tglTran ,case when tipe_bayar='000001' then 'CASH' ELSE 'QRIS' end tipeBayarNm
        FROM tr_ppob_mobile_master  WHERE sts='SUCCESS' and  DATE(dateIns) BETWEEN DATE(?) AND DATE(?) AND kop_id LIKE ?  order by dateIns desc
        ";
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir, $kop_id]);
        return json_encode($results);
    }

    public function selAllItem(Request $request, $cmd)
    {
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $toko_id = '%' . $_POST["toko_id"] . '%';
        $kategori = '%' . $_POST["kategori"] . '%';
        $itemNm = '%' . $_POST["itemNm"] . '%';
        $sql = " ";
            $sql = "
            SELECT *,ifnull(foto,'') as fotoNm FROM mst_barang WHERE toko_id LIKE '" . $toko_id . "' and kop_id LIKE '" . $kop_id . "'  and kategori like '" . $kategori . "'  and nama_barang LIKE '" . $itemNm . "'
            ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selClosing(Request $request, $cmd)
    {
        $kop_id = $_POST["kop_id"];
        $sql = "
            SELECT *,'Total Penjualan' item ,'BELUM CLOSING' ket,convert(NOW(),DATE) tgl,ifnull(SUM(real_amount),0) amount FROM tr_pos_master WHERE tipe_bayar='000001' AND convert(dateIns,DATE)=convert(NOW(),DATE) AND appYn='UNAPPROVE' AND  kop_id LIKE '%" . $kop_id . "%'
            UNION all
            SELECT *,'CASH' ,'BELUM CLOSING',convert(NOW(),DATE) ,ifnull(SUM(real_amount),0) amount FROM tr_pos_master WHERE tipe_bayar='000001' AND convert(dateIns,DATE)=convert(NOW(),DATE) AND appYn='UNAPPROVE' AND  kop_id LIKE '%" . $kop_id . "%'
            UNION all
            SELECT *,'QRIS','BELUM CLOSING',convert(NOW(),DATE) ,ifnull(SUM(real_amount),0) amount FROM tr_pos_master WHERE tipe_bayar='000002' AND convert(dateIns,DATE)=convert(NOW(),DATE) AND appYn='UNAPPROVE' AND  kop_id LIKE '%" . $kop_id . "%'
            UNION all
            SELECT *,'Total Penjualan' ,'CLOSING',convert(NOW(),DATE) ,ifnull(SUM(real_amount),0) amount FROM tr_pos_master WHERE tipe_bayar='000001' AND convert(dateIns,DATE)=convert(NOW(),DATE) AND appYn='APPROVE' AND  kop_id LIKE '%" . $kop_id . "%'
            UNION all
            SELECT *,'CASH' ,'CLOSING',convert(NOW(),DATE) ,ifnull(SUM(real_amount),0) amount FROM tr_pos_master WHERE tipe_bayar='000001' AND convert(dateIns,DATE)=convert(NOW(),DATE) AND appYn='APPROVE' AND  kop_id LIKE '%" . $kop_id . "%'
            UNION all
            SELECT *,'QRIS','CLOSING',convert(NOW(),DATE) ,ifnull(SUM(real_amount),0) amount FROM tr_pos_master WHERE tipe_bayar='000002' AND convert(dateIns,DATE)=convert(NOW(),DATE) AND appYn='APPROVE' AND  kop_id LIKE '%" . $kop_id . "%'
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selClosingByDate(Request $request, $cmd)
    {
        $kop_id = $_POST["kop_id"];
        $tgl_awal = $_POST["tgl_awal"];
        $sql = "
    SELECT 'Total Penjualan' AS item, 'BELUM CLOSING' AS ket, '" . $tgl_awal . "' AS tgl, ifnull(SUM(real_amount), 0) AS amount
    FROM tr_pos_master
    WHERE tipe_bayar='000001'
    AND convert(dateIns, DATE)='" . $tgl_awal . "'
    AND appYn='UNAPPROVE'
    AND kop_id LIKE '%" . $kop_id . "%'

    UNION ALL

    SELECT 'CASH' AS item, 'BELUM CLOSING' AS ket, '" . $tgl_awal . "' AS tgl, ifnull(SUM(real_amount), 0) AS amount
    FROM tr_pos_master
    WHERE tipe_bayar='000001'
    AND convert(dateIns, DATE)='" . $tgl_awal . "'
    AND appYn='UNAPPROVE'
    AND kop_id LIKE '%" . $kop_id . "%'

    UNION ALL

    SELECT 'QRIS' AS item, 'BELUM CLOSING' AS ket, '" . $tgl_awal . "' AS tgl, ifnull(SUM(real_amount), 0) AS amount
    FROM tr_pos_master
    WHERE tipe_bayar='000002'
    AND convert(dateIns, DATE)='" . $tgl_awal . "'
    AND appYn='UNAPPROVE'
    AND kop_id LIKE '%" . $kop_id . "%'

    UNION ALL

    SELECT 'Total Penjualan' AS item, 'CLOSING' AS ket, '" . $tgl_awal . "' AS tgl, ifnull(SUM(real_amount), 0) AS amount
    FROM tr_pos_master
    WHERE tipe_bayar='000001'
    AND convert(dateIns, DATE)='" . $tgl_awal . "'
    AND appYn='APPROVE'
    AND kop_id LIKE '%" . $kop_id . "%'

    UNION ALL

    SELECT 'CASH' AS item, 'CLOSING' AS ket, '" . $tgl_awal . "' AS tgl, ifnull(SUM(real_amount), 0) AS amount
    FROM tr_pos_master
    WHERE tipe_bayar='000001'
    AND convert(dateIns, DATE)='" . $tgl_awal . "'
    AND appYn='APPROVE'
    AND kop_id LIKE '%" . $kop_id . "%'

    UNION ALL

    SELECT 'QRIS' AS item, 'CLOSING' AS ket, '" . $tgl_awal . "' AS tgl, ifnull(SUM(real_amount), 0) AS amount
    FROM tr_pos_master
    WHERE tipe_bayar='000002'
    AND convert(dateIns, DATE)='" . $tgl_awal . "'
    AND appYn='APPROVE'
    AND kop_id LIKE '%" . $kop_id . "%'
";
        $results = DB::select($sql);
        return json_encode($results);
    }
    public function selPosMobileCore(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id =  $_POST["kop_id"];
        $toko_id =  $_POST["toko_id"];
        $sts = $_POST["sts"];
        $sql = "
            SELECT *,case when ifnull(c.nama,'')='' then ifnull(d.namaUser,'') ELSE ifnull(c.nama,'') END nama,b.nama_koperasi,DATE(a.dateins) transactionDate,e.detailName strTipebayar
            FROM tr_pos_mobile_master a
            JOIN mst_koperasi b ON a.kop_id=b.idx
            LEFT JOIN mst_anggota c ON a.no_anggota=c.no_anggota AND c.no_koperasi='" . $kop_id . "'
            LEFT JOIN mst_user d ON a.no_anggota=d.userId AND d.no_koperasi='" . $kop_id . "'
            LEFT JOIN mst_code_detail e ON e.masterCode='1000018'   and a.tipe_bayar=e.detailcode
            LEFT JOIN mst_toko f ON a.kop_id=f.kop_id AND f.kop_id='" . $kop_id . "' AND f.toko_id LIKE '%" . $toko_id . "%'
            where  DATE(a.dateins) BETWEEN DATE(?) AND DATE(?) AND a.kop_id LIKE '%" . $kop_id . "%'  and sts='" . $sts . "'  order by nama,dateins desc
        ";
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir]);
        return json_encode($results);
    }
    public function selPosBarcode(Request $request, $cmd)
    {
        $kop_id = $_POST["kop_id"];
        $pk =  $_POST["pk"];
        $sql = "
        SELECT *,((a.harga*qty)-discount) amount
        FROM tr_pos_barcode a
        JOIN mst_barang b ON a.kode_barang=b.idx
        where  a.pos_id='" . $pk . "' and  a.kop_id='" . $kop_id . "'
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
}
