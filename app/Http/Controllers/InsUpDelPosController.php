<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsUpDelPosController extends Controller
{
    //
    public function run(Request $request)
    {
        $cmd = $request->get("cmd");
        $nowe = $request->get("nowe");
        if ($cmd == "insPos") {
            $result = $this->insPos($request, $cmd, $nowe);
        } else if ($cmd == "delPos") {
            $result = $this->delPos($request, $cmd, $nowe);
        } else if ($cmd == "delPosAll") {
            $result = $this->delPosAll($request, $cmd, $nowe);
        } else if ($cmd == "insPosSimple") {
            $result = $this->insPosSimple($request, $cmd, $nowe);
        } else if ($cmd == "insPosSimpleMaster") {
            $result = $this->insPosSimpleMaster($request, $cmd, $nowe);
        } else if ($cmd == "insPosSimpleMobileMaster") {
            $result = $this->insPosSimpleMobileMaster($request, $cmd, $nowe);
        } else if ($cmd == "insPpobSimpleMaster") {
            $result = $this->insPpobSimpleMaster($request, $cmd, $nowe);
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

    public function insPos(Request $request, $cmd, $nowe)
    {
        $pk = $_POST["pk"];
        $userIns = $_POST["userIns"];
        $barcode = $_POST["barcode"];
        $kop_id = $_POST["kop_id"];

        $sql = " SELECT c.*,b.tanggal_produksi,b.tanggal_kadaluarsa,b.harga,b.packing_qty,a.*
        FROM  mst_barang_rutin_detail a
        JOIN mst_barang_rutin b ON a.kode_barang=b.kode_barang AND a.kop_id=b.kop_id AND a.barcode=b.barcode
        JOIN mst_barang c ON a.kop_id=c.kop_id AND a.kode_barang=c.idx
        WHERE a.kop_id='" . $kop_id . "'  AND a.generate_barcode='" . $barcode . "' ORDER BY a.sequence
        ";

        $resultMax = DB::select($sql);
        if (count($resultMax) > 0) {
            $sql = " SELECT * FROM  tr_pos_barcode WHERE pos_id='" . $pk . "'  AND generate_barcode='" . $barcode . "' AND kop_id='" . $kop_id . "'   ";
            $resultCek = DB::select($sql);
            if (count($resultCek) > 0) {
                $sql = " update  tr_pos_barcode set qty=qty+1 WHERE pos_id='" . $pk . "'  AND generate_barcode='" . $barcode . "' AND kop_id='" . $kop_id . "'  ";
                $resultIns = DB::update($sql);
            } else {
                $sql = "
                INSERT INTO tr_pos_barcode (pos_id,kode_barang,generate_barcode,kop_id,harga,discount,discount_value,qty,ppn,userIns,dateIns,dateServer )
                VALUES ('" . $pk . "','" . $resultMax[0]->kode_barang . "','" . $resultMax[0]->generate_barcode . "','" . $kop_id . "',
                '" . $resultMax[0]->harga . "',
                case
                when DATEDIFF(DATE(NOW()),'" . $resultMax[0]->tanggal_kadaluarsa . "' )=1 then (25*" . $resultMax[0]->harga . "/100)
                when DATEDIFF(DATE(NOW()),'" . $resultMax[0]->tanggal_kadaluarsa . "' )=2 then (50*" . $resultMax[0]->harga . "/100)
                else 0
                end,
                case
                when DATEDIFF(DATE(NOW()),'" . $resultMax[0]->tanggal_kadaluarsa . "' )=1 then '25%'
                when DATEDIFF(DATE(NOW()),'" . $resultMax[0]->tanggal_kadaluarsa . "' )=2 then '50%'
                else 0
                end,
                '1','0','" . $userIns . "',NOW(),
                case when ifnull(CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta'),'')='' then now() else CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta') end  )  ";
                $resultIns = DB::insert($sql);
            }
            if ($resultIns > 0) {
                $sql = " SELECT *,((a.harga*qty)-discount) amount
                FROM tr_pos_barcode a
                JOIN mst_barang b ON a.kode_barang=b.idx
                where  pos_id='" . $pk . "' ";
                $resutlLogin = DB::select($sql);
                return json_encode($resutlLogin);
            } else {
                $resutlMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
                return json_encode($resutlMsg);
            }
        } else {

            $resutlMsg = array("sts" => "N", "desc" => "Kode Barang Tidak Ada");
            return json_encode($resutlMsg);
        }
    }

    public function delPos(Request $request, $cmd)
    {
        $kop_id = $_POST["kop_id"];
        $pos_id = $_POST["pos_id"];
        $generate_barcode = $_POST["generate_barcode"];
        $sql = " DELETE FROM  tr_pos_barcode  WHERE kop_id=? and pos_id=? and generate_barcode=? ";
        $results = DB::delete($sql, [$kop_id, $pos_id, $generate_barcode]);
        if ($results > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }

    public function delPosAll(Request $request, $cmd)
    {
        $kop_id = $_POST["kop_id"];
        $pos_id = $_POST["pos_id"];
        $sql = " DELETE FROM  tr_pos_barcode  WHERE kop_id=? and pos_id=?  ";
        $results = DB::delete($sql, [$kop_id, $pos_id]);
        if ($results > 0) {
            $resutlMsg = array("sts" => "OK", "desc" => " Delete Success !", "msg" => "");
        } else {
            $resutlMsg = array("sts" => "N", "desc" => " Delete Failed !", "msg" => $results);
        }
        return json_encode($resutlMsg);
    }

    public function insPosSimple(Request $request, $cmd, $nowe)
    {
        $pk = $_POST["pk"];
        $userIns = $_POST["userIns"];
        $kode = $_POST["kode"];
        $kop_id = $_POST["kop_id"];
        $qtyPos = $_POST["qtyPos"];


        $sql = "
        SELECT a.*,b.nama_koperasi FROM mst_barang a
        LEFT JOIN mst_koperasi b ON a.kop_id=b.idx
        WHERE a.idx = '" . $kode . "' and kop_id like '%" . $kop_id . "%' ";
        $resutlMax = DB::select($sql);




        //var_dump($resutlMax);

        if (sizeof($resutlMax) > 0) {
            $sql = " SELECT * FROM  tr_pos WHERE pos_id='" . $pk . "'  AND kode_barang='" . $kode . "' AND kop_id='" . $kop_id . "'   ";
            $resultCek = DB::select($sql);

            if (sizeof($resultCek) > 0) {
                $sql = " update  tr_pos set qty=" . $qtyPos . " WHERE pos_id='" . $pk . "'  AND kode_barang='" . $kode . "' AND kop_id='" . $kop_id . "'  ";
                $resultIns = DB::update($sql);
            } else {
                $sql = "
                INSERT INTO tr_pos VALUES ('" . $pk . "','" . $resutlMax[0]->idx . "','" . $resutlMax[0]->barcode_kemasan . "','" . $kop_id . "','" . $resutlMax[0]->harga_jual . "',
                '" . $qtyPos . "','" . $resutlMax[0]->ppn . "','" . $userIns . "','" . $nowe . "',
                case when ifnull(CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta'),'')='' then now() else CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta') end     )";
                //echo $sql;
                DB::insert($sql);
            }
            //if($resultIns>0){
            //$resutlMsg=array("sts"=>"OK","desc"=>"Save Success","msg"=>"");
            //$sql = " SELECT SUM(c.qty*c.harga) total FROM tr_pos c WHERE c.pos_id='" . $pk . "' and c.kop_id='" . $kop_id . "'    ";
            //$resultTotal = DB::select($sql);

            $sql = "
            SELECT a.nama_barang,b.nama_koperasi,c.*,(c.qty*c.harga) amount ,(c.qty*c.harga) total,ifnull(b.nama_koperasi,'') AS fac_name,ifnull(b.alamat,'') AS fac_address,c.dateIns as tglTran
            FROM tr_pos c
            JOIN   mst_barang a ON a.idx=c.kode_barang
            LEFT JOIN mst_koperasi b ON a.kop_id=b.idx
            WHERE c.kop_id like '%" . $kop_id . "%'  and c.pos_id='" . $pk . "' order by  dateIns ";

            $resutlLogin = DB::select($sql);
            return json_encode($resutlLogin);

            //}else{
            //    $resutlMsg=array("sts"=>"N","desc"=>"Save Failed","msg"=>$resultIns);
            //    return json_encode($resutlMsg);

            //}
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "Kode Barang Tidak Ada");
            return json_encode($resutlMsg);
        }
    }

    public function insPosSimpleMaster(Request $request, $cmd, $nowe)
    {
        $pk = $_POST["pk"];
        $userIns = $_POST["userIns"];
        $kop_id = $_POST["kop_id"];
        $toko_id = $_POST["toko_id"];
        $noAnngota = $_POST["noAnngota"];
        $nama_customer = $_POST["nama_customer"];
        $nomor_meja = $_POST["nomor_meja"];
        $amount = $_POST["amount"];
        $ppn = $_POST["ppn"];
        $discount = $_POST["discount"];
        if ($discount == "") {
            $discount = 0;
        }
        $real_amount = $_POST["real_amount"];
        $dibayar = $_POST["dibayar"];
        $kembalian = $_POST["kembalian"];
        $total_item = $_POST["total_item"];
        $total_item = preg_replace('/\s+/', '', $total_item);
        $tipe_bayar = $_POST["tipe_bayar"];
        $ket = $_POST["ket"];

        $sql = "
        SELECT a.*,b.nama_barang from  tr_pos a
        JOIN mst_barang b ON a.kode_barang=b.idx
        WHERE a.pos_id='" . $pk . "'
        ";

        $resutlMax = DB::select($sql);
        if (count($resutlMax) > 0) {
            for ($x = 0; $x < count($resutlMax); $x++) {
                $sql = "
                INSERT INTO tr_pos_detail VALUES ('" . $resutlMax[$x]->pos_id . "','" . $resutlMax[$x]->kode_barang . "','" . $resutlMax[$x]->barcode . "',
                '" . $resutlMax[$x]->kop_id . "','" . $resutlMax[$x]->harga . "','" . $resutlMax[$x]->qty . "','" . $resutlMax[$x]->ppn . "','" . $userIns . "', '" . $nowe . "',
                case when ifnull(CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta'),'')='' then now() else CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta') end    )
                ";
                $resultIns = DB::insert($sql);
            }

            if ($resultIns > 0) {

                $sql = "
                SELECT ifnull(MAX(noAntrian),0)+1 noAntrian FROM  tr_pos_master WHERE  kop_id='" . $kop_id . "' AND userIns='" . $userIns . "'  and DATE(dateins)=DATE( '" . $nowe . "')
                ";

                $resutlMaxCnt = DB::select($sql);

                $sql = "
                    INSERT INTO tr_pos_master VALUES (ifnull(CONCAT('POS-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) ),CONCAT('POS-',SUBSTRING(DATE_FORMAT(NOW(3), '%Y%m%d%H%m%s-%f'),1,22) )),'" . $kop_id . "','" . $noAnngota . "','" . $toko_id . "','" . $nama_customer . "','" . $resutlMaxCnt[0]->noAntrian . "','" . $nomor_meja . "','" . $amount . "',
                    '" . $ppn . "','" . $discount . "','" . $real_amount . "',
                    '" . $dibayar . "','" . $kembalian . "','" . $total_item . "','" . $tipe_bayar . "','" . $ket . "','SUCCESS','UNAPPROVE','UNVERIFY','',null,'" . $userIns . "', '" . $nowe . "',
                    case when ifnull(CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta'),'')='' then now() else CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta') end  )
                    ";
                $resultTotal = DB::insert($sql);

                $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => $resutlMaxCnt[0]->noAntrian);
                return json_encode($resutlMsg);
            } else {
                $resutlMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
                return json_encode($resutlMsg);
            }
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "Kode Barang Tidak Ada");
            return json_encode($resutlMsg);
        }
    }
    public function insPosSimpleMobileMaster(Request $request, $cmd, $nowe)
    {
        $userIns = $_POST["userIns"];
        $kop_id = $_POST["kop_id"];
        $dibayar = $_POST["dibayar"];
        $kembalian = $_POST["kembalian"];
        $pos_id = $_POST["pos_id"];
        $sts = $_POST["sts"];

        $sql = "
        SELECT * from tr_pos_mobile_master
        WHERE pos_id='" . $pos_id . "'
        ";

        $resutlMax = DB::select($sql);
        if (count($resutlMax) > 0) {
            $sql = " update  tr_pos_mobile_master set dibayar=" . $dibayar . ",kembalian=" . $kembalian . ",sts='" . $sts . "' WHERE pos_id='" . $pos_id . "'  ";
            DB::update($sql);
            $resutlMsg = array("sts" => "OK", "desc" => "Update Success", "msg" => "");
            return json_encode($resutlMsg);
        } else {
            $resutlMsg = array("sts" => "N", "desc" => "Id Transaksi Tidak Ada");
            return json_encode($resutlMsg);
        }
    }

    public function insPpobSimpleMaster(Request $request, $cmd)
    {
        $userIns = $_POST["userIns"];
        $no_anggota = $_POST["no_anggota"];
        $kop_id = $_POST["kop_id"];
        $tipe = $_POST["tipe"];
        $dibayar = $_POST["dibayar"];
        $kembalian = $_POST["kembalian"];
        $nowe = $_POST["nowe"];
        $TransactionId = $_POST["TransactionId"];
        $param = $_POST["param"];
        $sts = "";
        $app = "";
        $verify = "";
        $strNowe = "STR_TO_DATE('" . $nowe . "','%Y-%m-%dT%H:%i:%s.%f') ";

        if ($tipe == "000001") {
            $sts = "SUCCESS";
            $app = "APPROVE";
            $verify = "VERIFY";
        } else {
            $sts = "WAITING";
            $app = "UNAPPROVE";
            $verify = "UNVERIFY";
        }
        $sql = "
        SELECT ifnull(CONCAT('PPOB-',SUBSTRING(DATE_FORMAT(" . $strNowe . ", '%Y%m%d%H%m%s-%f'),1,22) ),CONCAT('PPOB-',SUBSTRING(DATE_FORMAT(" . $strNowe . ", '%Y%m%d%H%m%s-%f'),1,22) )) ppob_id
        ";
        //echo $sql;
        $resutlMax = DB::select($sql);
        $obj = json_decode($param);

        if (isset($obj->tipeTransaksi)) {

            $sql = "
                INSERT INTO tr_ppob_detail VALUES ('" . $resutlMax[0]->ppob_id . "','" . $obj->Code . "','" . $no_anggota . "',
                '" . $kop_id . "','" . $obj->Account . "','" . $obj->Name . "','" .  $obj->Category . "','" . $userIns . "','" . str_replace("T", "", $nowe) . "',
                case when ifnull(CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta'),'')='' then now() else CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta') end )
                ";
            $resultIns = DB::insert($sql);

            if ($resultIns > 0) {
                $sql = "
                INSERT INTO tr_ppob_master VALUES ('" . $resutlMax[0]->ppob_id . "','" . $kop_id . "','" . $no_anggota . "','" . $obj->Code . "',
                '" . $obj->Nominal . "','" . $obj->Price . "','" . $obj->Fee . "',
                'ReqId','" . $obj->Account . "','" . $TransactionId . "',
                'Time', case when '" . $obj->Nominal . "'='' then 0 ELSE 0 end ,'Ref','IDR','0','0', case when '" . $obj->Nominal . "'='' then 0 ELSE " . $obj->Nominal . " end ,
                '" . $dibayar . "','" . $kembalian . "','1','" . $tipe . "','','" . $sts . "','','INSERT','" . $app . "','" . $verify . "','',null,'" . $userIns . "','" . str_replace("T", "", $nowe) . "',
                case when ifnull(CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta'),'')='' then now() else CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta') end )
                ";
                $resultMaster = DB::insert($sql);
                if ($resultMaster > 0) {
                    $resutlMsg = array("sts" => "OK", "desc" => "Transaksi Berhasil", "msg" => $resutlMax[0]->ppob_id);
                } else {
                    $resutlMsg = array("sts" => "N", "desc" => "Save Failed !!", "msg" => $resutlMax[0]->ppob_id);
                }
                return json_encode($resutlMsg);
            } else {
                $resutlMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
                return json_encode($resutlMsg);
            }
        } else {
            $data = $obj->data;
            $params = $data->params;
            //print_r($params) ;
            //echo $params[2]->Name;

            for ($x = 0; $x < count($params); $x++) {
                $sql = "
                INSERT INTO tr_ppob_detail VALUES ('" . $resutlMax[0]->ppob_id . "','" . $data->productCode . "','" . $no_anggota . "',
                '" . $kop_id . "','" . $data->Account . "','" . $params[$x]->Name . "','" .  $params[$x]->Value . "','" . $userIns . "','" . str_replace("T", "", $nowe) . "',
                case when ifnull(CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta'),'')='' then now() else CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta') end)
                ";
                $resultIns = DB::insert($sql);
            }

            if ($resultIns > 0) {
                $sql = "
                INSERT INTO tr_ppob_master VALUES ('" . $resutlMax[0]->ppob_id . "','" . $kop_id . "','" . $no_anggota . "','" . $data->productCode . "',
                '" . $data->nominal . "','" . $data->price . "','" . $data->serviceFee . "',
                '" . $data->RequestId . "','" . $data->Account . "','" . $TransactionId . "',
                '" . $data->Time . "', case when '" . $data->Amount . "'='' then 0 ELSE 0 end ,'" . $data->ReffId . "','" . $data->Currency . "','0','0', case when '" . $data->Amount . "'='' then 0 ELSE 0 end ,
                '" . $dibayar . "','" . $kembalian . "','1','" . $tipe . "','','" . $sts . "','','INSERT','" . $app . "','" . $verify . "','',null,'" . $userIns . "','" . str_replace("T", "", $nowe) . "',
                case when ifnull(CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta'),'')='' then now() else CONVERT_TZ(NOW(), 'UTC', 'Asia/jakarta') end )
                ";
                $resultMaster = DB::insert($sql);
                if ($resultMaster > 0) {
                    $resutlMsg = array("sts" => "OK", "desc" => "Save Success", "msg" => $resutlMax[0]->ppob_id);
                } else {
                    $resutlMsg = array("sts" => "N", "desc" => "Save Failed !!", "msg" => $resutlMax[0]->ppob_id);
                }
                return json_encode($resutlMsg);
            } else {
                $resutlMsg = array("sts" => "N", "desc" => "Save Failed", "msg" => $resultIns);
                return json_encode($resutlMsg);
            }
        }
    }
}
