<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryErpController extends Controller
{

    public function run(Request $request)
    {
        $cmd = $request->get("cmd");
        if ($cmd == "selNomorPo") {
            $result = $this->selNomorPo($request, $cmd);
        } else if ($cmd == "selPoMaster") {
            $result = $this->selPoMaster($request, $cmd);
        } else if ($cmd == "selPoDetail") {
            $result = $this->selPoDetail($request, $cmd);
        } else if ($cmd == "selNomorDo") {
            $result = $this->selNomorDo($request, $cmd);
        } else if ($cmd == "selDoMaster") {
            $result = $this->selDoMaster($request, $cmd);
        } else if ($cmd == "selDoDetail") {
            $result = $this->selDoDetail($request, $cmd);
        } else if ($cmd == "selNomorGrn") {
            $result = $this->selNomorGrn($request, $cmd);
        } else if ($cmd == "selGrnMaster") {
            $result = $this->selGrnMaster($request, $cmd);
        } else if ($cmd == "selGrnDetail") {
            $result = $this->selGrnDetail($request, $cmd);
        } else if ($cmd == "selPoDetailGrn") {
            $result = $this->selPoDetailGrn($request, $cmd);
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



    public function selNomorPo(Request $request, $cmd)
    {
        $sql = "
        SELECT ifnull(CONCAT('PO-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),
        CONCAT('PO-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noPo,
        ifnull(max(convert(REPLACE('','PO-',''),DECIMAL))+1,1) agtNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selPoDetail(Request $request, $cmd)
    {
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $po_no =  $_POST["po_no"];
        $sql = "
        SELECT a.*,b.nama_barang FROM tr_po_detail a
        JOIN mst_barang b ON a.kode_barang=b.idx
        WHERE a.kop_id like ? AND a.po_no=?
        ";
        $results = DB::select($sql, [$kop_id, $po_no]);
        return json_encode($results);
    }

    public function selPoMaster(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id = '%' . $_POST["kop_id"] . '%';

        $sql = "
        SELECT *
        FROM tr_po_master a
        JOIN mst_suplier b ON a.suplier_no=b.kode_suplier AND a.kop_id=b.kop_id
        WHERE  date(tgl_po)  BETWEEN ? AND ?  and a.kop_id like  ?   order by tgl_po
        ";
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir, $kop_id]);
        return json_encode($results);
    }


    public function selNomorDo(Request $request, $cmd)
    {
        $sql = "
        SELECT ifnull(CONCAT('DO-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),
        CONCAT('DO-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noDo,
        ifnull(max(convert(REPLACE('','DO-',''),DECIMAL))+1,1) agtNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selDoMaster(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id = '%' . $_POST["kop_id"] . '%';

        $sql = "
        SELECT *
        FROM tr_do_master a
        JOIN mst_pembeli b ON a.pembeli_no=b.kode_pembeli AND a.kop_id=b.kop_id
        WHERE  date(tgl_do)  BETWEEN ? AND ?  and a.kop_id like  ?   order by tgl_do
        ";
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir, $kop_id]);
        return json_encode($results);
    }

    public function selDoDetail(Request $request, $cmd)
    {
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $do_no =  $_POST["do_no"];
        $sql = "
        SELECT a.*,b.nama_barang
		FROM tr_do_detail a
        JOIN mst_barang b ON a.kode_barang=b.idx
        WHERE a.kop_id like ? AND a.do_no=?
        ";
        $results = DB::select($sql, [$kop_id, $do_no]);
        return json_encode($results);
    }

    public function selNomorGrn(Request $request, $cmd)
    {
        $sql = "
        SELECT ifnull(CONCAT('GRN-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),
        CONCAT('GRN-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )) as noGrn,
        ifnull(max(convert(REPLACE('','GRN-',''),DECIMAL))+1,1) agtNumber
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selGrnMaster(Request $request, $cmd)
    {
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        $kop_id = '%' . $_POST["kop_id"] . '%';

        $sql = "
        SELECT *
        FROM tr_grn_master a
        JOIN mst_suplier b ON a.suplier_no=b.kode_suplier AND a.kop_id=b.kop_id
        WHERE  date(tgl_grn)  BETWEEN ? AND ?  and a.kop_id like  ?   order by tgl_grn
        ";
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir, $kop_id]);
        return json_encode($results);
    }

    public function selGrnDetail(Request $request, $cmd)
    {
        $kop_id = '%' . $_POST["kop_id"] . '%';
        $grn_no =  $_POST["grn_no"];
        $sql = "
        SELECT a.*,b.nama_barang
		FROM tr_grn_detail a
        JOIN mst_barang b ON a.kode_barang=b.idx
        WHERE a.kop_id like ? AND a.grn_no=?
        ";
        $results = DB::select($sql, [$kop_id, $grn_no]);
        return json_encode($results);
    }

    public function selPoDetailGrn(Request $request, $cmd)
    {
        $kop_id = '%' . $_POST["kop_id"] . '%';
        //$po_no =  $_POST["po_no"];
        $sql = "
        SELECT a.po_no,a1.tgl_po,a1.suplier_no,b.* ,b.detailName nmKategory,c.detailName nmUnit ,d.nama_koperasi,'' nama_toko,e.detailName nmMerk
        from
        tr_po_detail a
        JOIN tr_po_master a1 ON a.po_no=a1.no_po AND a1.kop_id LIKE '" . $kop_id . "'
            JOIN mst_barang b ON a.kode_barang=b.idx AND b.kop_id LIKE '" . $kop_id . "'
            JOIN mst_suplier b1 ON a1.suplier_no=b1.kode_suplier AND b1.kop_id LIKE '" . $kop_id . "'
        LEFT JOIN (SELECT * from mst_code_detail where masterCode='1000014'  ) b ON b.kategori=b.detailCode
        LEFT JOIN (SELECT * from mst_code_detail WHERE masterCode='1000013' and detailkop LIKE '" . $kop_id . "' ) c ON b.unit=c.detailCode
        LEFT JOIN mst_koperasi d ON a.kop_id=d.idx
        LEFT JOIN (SELECT * from mst_code_detail WHERE masterCode='1000016' and detailkop LIKE '" . $kop_id . "'  ) e ON b.merk=e.detailCode
        where    a.kop_id LIKE '" . $kop_id . "' order by  a1.tgl_po desc
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }
}

