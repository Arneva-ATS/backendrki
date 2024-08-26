<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FelloController extends Controller
{
    //
    public function run(Request $request)
    {

        //Trial Host
        $host = "http://35.187.249.186/";



        $cmd = $request->get("cmd");
        if ($cmd == "selMasterFello") {
            $result = $this->selMasterFello($request, $cmd);
        } else if ($cmd == "felloSignatureAuth") {
            $result = $this->felloSignatureAuth($request, $cmd, $host);
        } else if ($cmd == "selWalletFello") {
            $result = $this->selWalletFello($request, $cmd, $host);
        } else if ($cmd == "selWalletFelloBinding") {
            $result = $this->selWalletFelloBinding($request, $cmd, $host);
        } else if ($cmd == "selWalletFelloCekBinding") {
            $result = $this->selWalletFelloCekBinding($request, $cmd, $host);
        } else if ($cmd == "felloAccountBinding") {
            $result = $this->felloAccountBinding($request, $cmd, $host);
        } else if ($cmd == "directDebitH2H") {
            $result = $this->directDebitH2H($request, $cmd, $host);
        } else if ($cmd == "felloBalanceInquery") {
            $result = $this->felloBalanceInquery($request, $cmd, $host);
        } else if ($cmd == "felloPlnPostPaid") {
            $result = $this->felloPlnPostPaid($request, $cmd, $host);
        } else if ($cmd == "felloMultiBiller") {
            $result = $this->felloMultiBiller($request, $cmd, $host);
        } else if ($cmd == "felloMultiBillerPostPaid") {
            $result = $this->felloMultiBillerPostPaid($request, $cmd, $host);
        } else if ($cmd == "felloPlnReversal") {
            $result = $this->felloPlnReversal($request, $cmd, $host);
        } else if ($cmd == "felloPlnReversalExecute") {
            $result = $this->felloPlnReversalExecute($request, $cmd, $host);
        } else if ($cmd == "felloMultiBillerReversal") {
            $result = $this->felloMultiBillerReversal($request, $cmd, $host);
        } else if ($cmd == "felloMultiBillerReversalExecute") {
            $result = $this->felloMultiBillerReversalExecute($request, $cmd, $host);
        } else if ($cmd == "felloWalletSukses") {
            $result = $this->felloWalletSukses($request, $cmd, $host);
        } else if ($cmd == "selHistoryWalletFello") {
            $result = $this->selHistoryWalletFello($request, $cmd, $host);
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

    public function selMasterFello(Request $request, $cmd)
    {
        $tipe = $_POST["tipe"];
        $kode_layanan = $_POST["kode_layanan"];
        $sql = "
        SELECT * FROM mst_fello_multi_biller
        where tipe like '%" . $tipe . "%' and kode_layanan like '%" . $kode_layanan . "%'
        ORDER BY tipe,kode_layanan,kode_biller,product_id
        ";

        $results = DB::select($sql);
        return json_encode($results);
    }

    public function selWalletFello(Request $request, $cmd)
    {
        $kop_id = $_POST["kop_id"];
        $traceNumber = $_POST["traceNumber"];
        $sql = "
        SELECT a.*,b.sts,b.response
        FROM
        tr_fello_wallet a
        JOIN tr_log_fello_wallet b ON a.traceNumber=b.traceNumber
        WHERE  b.sts='Transaction completed' AND a.traceNumber='" . $traceNumber . "' and a.kop_id='" . $kop_id . "'
        ";
        //SELECT * FROM   tr_log_fello_wallet where kop_id='" . $kop_id . "' and traceNumber='" . $traceNumber . "' and sts='Transaction completed';
        $results = DB::select($sql);

        return json_encode($results);
    }


    public function selHistoryWalletFello(Request $request, $cmd)
    {
        $kop_id = $_POST["kop_id"];
        $tgl_awal = $_POST["tgl_awal"];
        $tgl_akhir = $_POST["tgl_akhir"];
        //$traceNumber = $_POST["traceNumber"];
        $sql = "
        SELECT a.*,c.nama_koperasi,b.sts,b.response
        FROM
        tr_fello_wallet a
        JOIN tr_log_fello_wallet b ON a.traceNumber=b.traceNumber
        JOIN mst_koperasi c ON a.kop_id=c.idx
        WHERE  a.kop_id='" . $kop_id . "'  and date(tgl_tran)  BETWEEN ? AND ?     order by tgl_tran desc
        ";
        $results = DB::select($sql, [$tgl_awal, $tgl_akhir]);
        return json_encode($results);
    }

    public function selWalletFelloBinding(Request $request, $cmd)
    {
        $kop_id = $_POST["kop_id"];
        $custId = $_POST["custId"];
        $sql = "
        SELECT * FROM tr_fello_wallet_log_binding  WHERE  sts='Transaction completed' AND noAccount='" . $custId . "' ORDER BY dateIns desc limit 1;
        ";
        $results = DB::select($sql);

        return json_encode($results);
    }
    public function selWalletFelloCekBinding(Request $request, $cmd)
    {
        $kop_id = $_POST["kop_id"];
        $userId = $_POST["userId"];


        $sql = "
        SELECT a.*,b.*
        FROM mst_user a
        JOIN mst_koperasi b ON a.no_koperasi=b.idx
        where a.userId='" . $userId . "' and b.idx='" . $kop_id . "'
        ";

        $res = DB::select($sql);
        $noAccount = $res[0]->noAccount;

        $sql = "
        SELECT * FROM tr_fello_wallet_log_binding  WHERE  sts='Transaction completed' AND noAccount='" . $noAccount . "' ORDER BY dateIns desc limit 1;
        ";
        $results = DB::select($sql);
        return json_encode($results);
    }



    public function felloSignatureAuth(Request $request, $cmd, $host)
    {
        $kop_id = $_POST["kop_id"];
        $userId = $_POST["userId"];

        $sql = "
        SELECT a.*,b.*
        FROM mst_user a
        JOIN mst_koperasi b ON a.no_koperasi=b.idx
        where a.userId='" . $userId . "' and b.idx='" . $kop_id . "'
        ";
        $results = DB::select($sql);

        $body = array(
            "xTimeStamp" =>  $results[0]->fello_x_timestamp,
            "xPrivateKey" =>  $results[0]->fello_x_private_key,
            "xClientKey" =>  $results[0]->fello_x_client_key,
            "xClientSecret" =>  $results[0]->fello_x_client_secret,
            "xPartnerId" =>  $results[0]->fello_x_partner_id,
        );

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $host . 'wallet.php?act=felloSignatureAuth',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function felloBalanceInquery(Request $request, $cmd, $host)
    {
        $xExternalId = $_POST["xExternalId"];
        //$token = $_POST["token"];

        $kop_id = $_POST["kop_id"];
        $userId = $_POST["userId"];

        $sql = "
        SELECT a.*,b.*
        FROM mst_user a
        JOIN mst_koperasi b ON a.no_koperasi=b.idx
        where a.userId='" . $userId . "' and b.idx='" . $kop_id . "'
        ";

        $results = DB::select($sql);

        $body = array(
            "xTimeStamp" =>  $results[0]->fello_x_timestamp,
            "xPrivateKey" =>  $results[0]->fello_x_private_key,
            "xClientKey" =>  $results[0]->fello_x_client_key,
            "xClientSecret" =>  $results[0]->fello_x_client_secret,
            "xPartnerId" =>  $results[0]->fello_x_partner_id,
        );

        $results = DB::select($sql);
        $noAccount = $results[0]->noAccount;
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $host . 'wallet.php?act=felloBalanceInquery&xExternalId=' . $xExternalId . '&noAccount=' . $noAccount,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function felloAccountBinding(Request $request, $cmd, $host)
    {
        $xExternalId = $_POST["xExternalId"];
        $userId = $_POST["userId"];
        $sts = $_POST["sts"];
        $kop_id = $_POST["kop_id"];


        $sql = "
        SELECT a.*,b.*
        FROM mst_user a
        JOIN mst_koperasi b ON a.no_koperasi=b.idx
        where a.userId='" . $userId . "' and b.idx='" . $kop_id . "'
        ";

        $results = DB::select($sql);
        $body = array(
            "xTimeStamp" =>  $results[0]->fello_x_timestamp,
            "xPrivateKey" =>  $results[0]->fello_x_private_key,
            "xClientKey" =>  $results[0]->fello_x_client_key,
            "xClientSecret" =>  $results[0]->fello_x_client_secret,
            "xPartnerId" =>  $results[0]->fello_x_partner_id,
        );
        $noAccount = $results[0]->noAccount;

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $host . 'wallet.php?act=felloAccountBinding&xExternalId=' . $xExternalId . '&noAccount=' . $noAccount . '&userId=' . $userId . '&kop_id=' . $kop_id . '&sts=' . $sts,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS =>  $body,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    public function directDebitH2H(Request $request, $cmd, $host)
    {
        $xExternalId = $_POST["xExternalId"];
        $token = $_POST["token"];
        $amount = $_POST["amount"];

        $kop_id = $_POST["kop_id"];
        $userId = $_POST["userId"];

        $sql = "
        SELECT a.*,b.*
        FROM mst_user a
        JOIN mst_koperasi b ON a.no_koperasi=b.idx
        where a.userId='" . $userId . "' and b.idx='" . $kop_id . "'
        ";

        $results = DB::select($sql);

        $body = array(
            "xTimeStamp" =>  $results[0]->fello_x_timestamp,
            "xPrivateKey" =>  $results[0]->fello_x_private_key,
            "xClientKey" =>  $results[0]->fello_x_client_key,
            "xClientSecret" =>  $results[0]->fello_x_client_secret,
            "xPartnerId" =>  $results[0]->fello_x_partner_id,
        );

        $results = DB::select($sql);
        $noAccount = $results[0]->noAccount;

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $host . 'wallet.php?act=directDebitH2H&xExternalId=' . $xExternalId . '&token=' . $token . '&noAccount=' . $noAccount . '&amount=' . $amount . '&kop_id=' . $kop_id . '&userIns=' . $userId,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


    public function felloPlnPostPaid(Request $request, $cmd, $host)
    {
        $date = $_POST["date"];
        $act = $_POST["act"];

        $mti_MessageTypeIdentification = $_POST["mti_MessageTypeIdentification"];
        $bit2_PrimaryAccountNumber = $_POST["bit2_PrimaryAccountNumber"];
        $bit3_ProcessingCode = $_POST["bit3_ProcessingCode"];
        $bit4_TransactionAmount = $_POST["bit4_TransactionAmount"];
        $bit7_TransmissionDateTime = $_POST["bit7_TransmissionDateTime"];
        $bit11_STAN = $_POST["bit11_STAN"];
        $bit12_LocalTransactionTime = $_POST["bit12_LocalTransactionTime"];
        $bit13_LocalTransactionDate = $_POST["bit13_LocalTransactionDate"];
        $bit15_SettlementDate = $_POST["bit15_SettlementDate"];
        $bit18_MerchantType = "6021";
        $bit32_AcquiringInstitutionIdentificationCode = "008";
        $bit37_RetrievalReferenceNumber = $_POST["bit37_RetrievalReferenceNumber"];
        //$bit39_ResponseCode=$_POST["bit39_ResponseCode"];
        $bit41_TerminalID = "DEVATS01";
        $bit42_AcceptorIdentification = "200900100800000";
        $bit48_AdditionalDataPrivate = $_POST["bit48_AdditionalDataPrivate"];
        $bit49_TransactionCurrencyCode = $_POST["bit49_TransactionCurrencyCode"];
        $bit63_DataLoket = $_POST["bit63_DataLoket"];

        $body = '';
        if ($act == "felloPlnPostPaidInq") {
            $body = array(
                "mti" =>  $mti_MessageTypeIdentification,
                "bit2" =>  $bit2_PrimaryAccountNumber,
                "bit3" =>  $bit3_ProcessingCode,
                "bit4" =>  $bit4_TransactionAmount,
                "bit7" =>  $bit7_TransmissionDateTime,
                "bit11" =>  $bit11_STAN,
                "bit12" =>  $bit12_LocalTransactionTime,
                "bit13" =>  $bit13_LocalTransactionDate,
                "bit15" =>  $bit15_SettlementDate,
                "bit18" =>  $bit18_MerchantType,
                "bit32" =>  $bit32_AcquiringInstitutionIdentificationCode,
                "bit37" =>  $bit37_RetrievalReferenceNumber,
                "bit41" =>  $bit41_TerminalID,
                "bit42" =>  $bit42_AcceptorIdentification,
                "bit48" =>  $bit48_AdditionalDataPrivate,
                "bit49" =>  $bit49_TransactionCurrencyCode,
                "bit63" => $bit63_DataLoket
            );

            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $host . 'plnPostPaid.php?act=felloPlnPostPaidInq',
                    CURLOPT_POST => 1,
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_CUSTOMREQUEST => 'POST',


                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        } else {
            $date = $_POST["date"];
            $act = $_POST["act"];
            $productCode = $_POST["productCode"];
            $account = $_POST["account"];
            $kop_id = $_POST["kop_id"];
            $userIns = $_POST["userIns"];
            $no_anggota = $_POST["no_anggota"];
            $ppob_id = $_POST["ppob_id"];
            $nama_customer = $_POST["nama_customer"];
            $amount = $_POST["amount"];
            $real_amount = $_POST["real_amount"];
            $dibayar = $_POST["dibayar"];
            $kembalian = $_POST["kembalian"];
            $total_item = $_POST["total_item"];
            $tipe = $_POST["tipe"];
            $tipe_transaksi = $_POST["tipe_transaksi"];
            $total_belanja = $_POST["total_belanja"];
            $kode_title = $_POST["kode_title"];
            $kode_bayar = $_POST["kode_bayar"];
            $ket = $_POST["ket"];
            $transactionId = $_POST["transactionId"];
            $nowe = $_POST["nowe"];
            $param = $_POST["param"];
            $nomorTran = $_POST["nomorTran"];



            $body = array(
                "mti" =>  $mti_MessageTypeIdentification,
                "bit2" =>  $bit2_PrimaryAccountNumber,
                "bit3" =>  $bit3_ProcessingCode,
                "bit4" =>  $bit4_TransactionAmount,
                "bit7" =>  $bit7_TransmissionDateTime,
                "bit11" =>  $bit11_STAN,
                "bit12" =>  $bit12_LocalTransactionTime,
                "bit13" =>  $bit13_LocalTransactionDate,
                "bit15" =>  $bit15_SettlementDate,
                "bit18" =>  $bit18_MerchantType,
                "bit32" =>  $bit32_AcquiringInstitutionIdentificationCode,
                "bit37" =>  $bit37_RetrievalReferenceNumber,
                "bit41" =>  $bit41_TerminalID,
                "bit42" =>  $bit42_AcceptorIdentification,
                "bit48" =>   str_replace('"', "`", $bit48_AdditionalDataPrivate),
                "bit49" =>  $bit49_TransactionCurrencyCode,
                "bit63" => $bit63_DataLoket
            );
            ///var_dump($body);

            $sql = " insert into tr_ppob_master (ppob_id,	kop_id,	no_anggota,	productCode,	nominal,	price,	serviceFee,	requestId,	account,
            transactionId,	time_,	amount,	refId,	currency,	ppn,	discount,	real_amount,	dibayar,
            kembalian,	total_item,	tipe_bayar,	ket,	sts,apiRequest,	apiProvider,	apiStsProvider,	appYn,	verify,	userVerify,	dateVerify,
            userIns,	dateIns,	dateServer )
            values (
            '" . $ppob_id . "',	'" . $kop_id . "',	'" . $no_anggota . "',	'" . $productCode . "',	0,	'" . $amount . "',	0,	'',	'" . $nomorTran . "',
            '" . $transactionId . "',	'',	'" . $amount . "',	'',	'',	0,	0,	'" . $real_amount . "',	'" . $dibayar . "',
            '" . $kembalian . "',	'" . $total_item . "',	'" . $tipe . "',	'',	'','" . str_replace("'", "`", json_encode($body)) . "',	'FELLO','OPEN',	'Y',	'N',	'',	null,
            '" . $userIns . "',	now(),	now()
            );            ";
            DB::insert($sql);

            //echo json_encode($body);
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $host . 'plnPostPaid.php?act=felloPlnPostPaidPayment',
                    CURLOPT_POST => 1,
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response, true);
            $bit39 = $res["bit39"];


            return $bit39;
        }
    }

    public function felloMultiBiller(Request $request, $cmd, $host)
    {
        $date = $_POST["date"];
        $act = $_POST["act"];
        $mti_MessageTypeIdentification = $_POST["mti_MessageTypeIdentification"];
        $bit2_PrimaryAccountNumber = $_POST["bit2_PrimaryAccountNumber"];
        $bit3_ProcessingCode = $_POST["bit3_ProcessingCode"];
        $bit4_TransactionAmount = $_POST["bit4_TransactionAmount"];
        $bit7_TransmissionDateTime = date('mdhis');
        $bit11_STAN = rand('000000', '999999');
        $bit12_LocalTransactionTime = date('his');
        $bit13_LocalTransactionDate = date('md');
        $bit15_SettlementDate = date('m') . str_pad(date('d') + 1, 2, "0", STR_PAD_LEFT);
        $bit18_MerchantType = "6021";
        $bit32_AcquiringInstitutionIdentificationCode = "008";
        $bit37_RetrievalReferenceNumber = "000000114415";
        //$bit39_ResponseCode=$_POST["bit39_ResponseCode"];
        $bit41_TerminalID = "DEVATS01";
        $bit42_AcceptorIdentification = "200900100800000";
        $bit48_AdditionalDataPrivate = $_POST["bit48_AdditionalDataPrivate"];
        $bit49_TransactionCurrencyCode = '360';
        $bit63_DataLoket = $_POST["bit63_DataLoket"];


        $productCode = $_POST["productCode"];
        $account = $_POST["account"];
        $kop_id = $_POST["kop_id"];
        $userIns = $_POST["userIns"];
        $no_anggota = $_POST["no_anggota"];
        $ppob_id = $_POST["ppob_id"];
        $nama_customer = $_POST["nama_customer"];
        $amount = $_POST["amount"];
        $real_amount = $_POST["real_amount"];
        $dibayar = $_POST["dibayar"];
        $kembalian = $_POST["kembalian"];
        $total_item = $_POST["total_item"];
        $tipe = $_POST["tipe"];
        $tipe_transaksi = $_POST["tipe_transaksi"];
        $total_belanja = $_POST["total_belanja"];
        $kode_title = $_POST["kode_title"];
        $kode_bayar = $_POST["kode_bayar"];
        $ket = $_POST["ket"];
        $nowe = $_POST["nowe"];
        $transactionId = $_POST["transactionId"];
        //$param = $_POST["param"];

        $body = array(
            "mti" =>  $mti_MessageTypeIdentification,
            "bit2" =>  $bit2_PrimaryAccountNumber,
            "bit3" =>  $bit3_ProcessingCode,
            "bit4" =>  $bit4_TransactionAmount,
            "bit7" =>  $bit7_TransmissionDateTime,
            "bit11" => $bit11_STAN,
            "bit12" => $bit12_LocalTransactionTime,
            "bit13" => $bit13_LocalTransactionDate,
            "bit15" => $bit15_SettlementDate,
            "bit18" => $bit18_MerchantType,
            "bit32" => $bit32_AcquiringInstitutionIdentificationCode,
            "bit37" => $bit37_RetrievalReferenceNumber,
            "bit41" => $bit41_TerminalID,
            "bit42" => $bit42_AcceptorIdentification,
            "bit48" => $bit48_AdditionalDataPrivate,
            "bit49" => $bit49_TransactionCurrencyCode,
        );


        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $host . 'multiBillerPrePaid.php',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => 1,
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response, true);

        return  $res;

    }
    public function felloMultiBillerPostPaid(Request $request, $cmd, $host)
    {
        $date = $_POST["date"];
        $act = $_POST["act"];
        $mti_MessageTypeIdentification = $_POST["mti_MessageTypeIdentification"];
        $bit2_PrimaryAccountNumber = $_POST["bit2_PrimaryAccountNumber"];
        $bit3_ProcessingCode = $_POST["bit3_ProcessingCode"];
        $bit4_TransactionAmount = $_POST["bit4_TransactionAmount"];
        $bit7_TransmissionDateTime = date('mdhis');
        $bit11_STAN = "";
        $bit12_LocalTransactionTime = date('his');
        $bit13_LocalTransactionDate = date('md');
        $bit15_SettlementDate = date('m') . str_pad(date('d') + 1, 2, "0", STR_PAD_LEFT);
        $bit18_MerchantType = "6021";
        $bit32_AcquiringInstitutionIdentificationCode = "008";
        $bit37_RetrievalReferenceNumber = '000000114415';
        //$bit39_ResponseCode=$_POST["bit39_ResponseCode"];
        $bit41_TerminalID = "DEVATS01";
        $bit42_AcceptorIdentification = "200900100800000";
        $bit48_AdditionalDataPrivate = $_POST["bit48_AdditionalDataPrivate"];
        $bit49_TransactionCurrencyCode = '360';
        $bit63_DataLoket = $_POST["bit63_DataLoket"];

        if ($act == "felloMultiPostPaidInq") {
            $bit11_STAN = rand('000000', '999999');
            $kop_id = $_POST["kop_id"];
            $userIns = $_POST["userIns"];
            $no_anggota = $_POST["no_anggota"];
            $ppob_id = $_POST["ppob_id"];
            $body = array(
                "mti" =>  $mti_MessageTypeIdentification,
                "bit2" =>  $bit2_PrimaryAccountNumber,
                "bit3" =>  $bit3_ProcessingCode,
                //"bit4" =>  $bit4_TransactionAmount,
                "bit7" =>  $bit7_TransmissionDateTime,
                "bit11" => $bit11_STAN,
                "bit12" => $bit12_LocalTransactionTime,
                "bit13" => $bit13_LocalTransactionDate,
                "bit15" => $bit15_SettlementDate,
                "bit18" => $bit18_MerchantType,
                "bit32" => $bit32_AcquiringInstitutionIdentificationCode,
                "bit37" => $bit37_RetrievalReferenceNumber,
                "bit41" => $bit41_TerminalID,
                "bit42" => $bit42_AcceptorIdentification,
                "bit48" => $bit48_AdditionalDataPrivate,
                "bit49" => $bit49_TransactionCurrencyCode,
            );


            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $host . 'multiBillerPostPaid.php?act=felloMultiPostPaidInq',
                    CURLOPT_POST => 1,
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_RETURNTRANSFER => 1,
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response, true);
            return $response;
        } else if ($act == "felloMultiPostPaidPayment") {
            $bit11_STAN = $_POST["bit11_STAN"];

            $productCode = $_POST["productCode"];
            $account = $_POST["account"];
            $kop_id = $_POST["kop_id"];
            $userIns = $_POST["userIns"];
            $no_anggota = $_POST["no_anggota"];
            $ppob_id = $_POST["ppob_id"];
            $nama_customer = $_POST["nama_customer"];
            $amount = $_POST["amount"];
            $real_amount = $_POST["real_amount"];
            $dibayar = $_POST["dibayar"];
            $kembalian = $_POST["kembalian"];
            $total_item = $_POST["total_item"];
            $tipe = $_POST["tipe"];
            $tipe_transaksi = $_POST["tipe_transaksi"];
            $total_belanja = $_POST["total_belanja"];
            $kode_title = $_POST["kode_title"];
            $kode_bayar = $_POST["kode_bayar"];
            $ket = $_POST["ket"];
            $nowe = $_POST["nowe"];
            $transactionId = $_POST["transactionId"];
            //$param = $_POST["param"];

            $body = array(
                "mti" =>  $mti_MessageTypeIdentification,
                "bit2" =>  $bit2_PrimaryAccountNumber,
                "bit3" =>  $bit3_ProcessingCode,
                "bit4" =>  $bit4_TransactionAmount,
                "bit7" =>  $bit7_TransmissionDateTime,
                "bit11" => $bit11_STAN,
                "bit12" => $bit12_LocalTransactionTime,
                "bit13" => $bit13_LocalTransactionDate,
                "bit15" => $bit15_SettlementDate,
                "bit18" => $bit18_MerchantType,
                "bit32" => $bit32_AcquiringInstitutionIdentificationCode,
                "bit37" => $bit37_RetrievalReferenceNumber,
                "bit41" => $bit41_TerminalID,
                "bit42" => $bit42_AcceptorIdentification,
                "bit48" => $bit48_AdditionalDataPrivate,
                "bit49" => $bit49_TransactionCurrencyCode,
            );


            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $host . 'multiBillerPostPaid.php?act=felloMultiPostPaidPayment',
                    CURLOPT_POST => 1,
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_RETURNTRANSFER => 1,
                )
            );

            $response = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response, true);

            return $res;
        }
    }

    public function felloPlnReversalExecute(Request $request, $cmd, $host)
    {
        $ppob_id = $_POST["ppob_id"];
        $act = $_POST["act"];
        $kop_id = $_POST["kop_id"];

        $mti_MessageTypeIdentification = $_POST["mti_MessageTypeIdentification"];
        $bit2_PrimaryAccountNumber = $_POST["bit2_PrimaryAccountNumber"];
        $bit3_ProcessingCode = $_POST["bit3_ProcessingCode"];
        $bit4_TransactionAmount = $_POST["bit4_TransactionAmount"];
        $bit7_TransmissionDateTime = $_POST["bit7_TransmissionDateTime"];
        $bit11_STAN = $_POST["bit11_STAN"];
        $bit12_LocalTransactionTime = $_POST["bit12_LocalTransactionTime"];
        $bit13_LocalTransactionDate = $_POST["bit13_LocalTransactionDate"];
        $bit15_SettlementDate = $_POST["bit15_SettlementDate"];
        $bit18_MerchantType = $_POST["bit18_MerchantType"];
        $bit32_AcquiringInstitutionIdentificationCode = $_POST["bit32_AcquiringInstitutionIdentificationCode"];
        $bit37_RetrievalReferenceNumber = $_POST["bit37_RetrievalReferenceNumber"];
        //$bit39_ResponseCode=$_POST["bit39_ResponseCode"];
        $bit41_TerminalID = $_POST["bit41_TerminalID"];
        $bit42_AcceptorIdentification = $_POST["bit42_AcceptorIdentification"];
        $bit48_AdditionalDataPrivate = $_POST["bit48_AdditionalDataPrivate"];
        $bit49_TransactionCurrencyCode = $_POST["bit49_TransactionCurrencyCode"];
        $bit61_DataTambahanBiller = $_POST["bit61_DataTambahanBiller"];
        $bit62_Data62 = $_POST["bit62_Data62"];
        $bit63_DataLoket = $_POST["bit63_DataLoket"];
        $bit90_OriginalDataElement = $_POST["bit90_OriginalDataElement"];

        $body = '';
        $body = array(
            "mti" =>  $mti_MessageTypeIdentification,
            "bit2" =>  $bit2_PrimaryAccountNumber,
            "bit3" =>  $bit3_ProcessingCode,
            "bit4" =>  $bit4_TransactionAmount,
            "bit7" =>  $bit7_TransmissionDateTime,
            "bit11" =>  $bit11_STAN,
            "bit12" =>  $bit12_LocalTransactionTime,
            "bit13" =>  $bit13_LocalTransactionDate,
            "bit15" =>  $bit15_SettlementDate,
            "bit18" =>  $bit18_MerchantType,
            "bit32" =>  $bit32_AcquiringInstitutionIdentificationCode,
            "bit37" =>  $bit37_RetrievalReferenceNumber,
            "bit41" =>  $bit41_TerminalID,
            "bit42" =>  $bit42_AcceptorIdentification,
            "bit48" =>  $bit48_AdditionalDataPrivate,
            "bit49" =>  $bit49_TransactionCurrencyCode,
            "bit61" => "",
            "bit62" => "",
            "bit63" => "",
            "bit90" => $bit90_OriginalDataElement
        );

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $host . 'plnPostPaid.php?act=felloPlnPostPaidPayment',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            )
        );
        $response = curl_exec($curl);

        $res = json_decode($response, true);
        curl_close($curl);
        return $res;
    }

    public function felloPlnReversal(Request $request, $cmd, $host)
    {
        $ppob_id = $_POST["ppob_id"];
        $kop_id = $_POST["kop_id"];
        $sql = "
        SELECT * FROM tr_ppob_master WHERE ppob_id='" . $ppob_id . "' AND kop_id='" . $kop_id . "'
        ";
        $results = DB::select($sql);
        $apiRequest =  $results[0]->apiRequest;
        return ($apiRequest);
    }

    public function felloMultiBillerReversal(Request $request, $cmd, $host)
    {
        $ppob_id = $_POST["ppob_id"];
        $kop_id = $_POST["kop_id"];
        $sql = "
        SELECT * FROM tr_ppob_master WHERE ppob_id='" . $ppob_id . "' AND kop_id='" . $kop_id . "'
        ";
        $results = DB::select($sql);
        $apiRequest = $results[0]->apiRequest;
        return $apiRequest;
    }

    public function felloMultiBillerReversalExecute(Request $request, $cmd, $host)
    {
        $ppob_id = $_POST["ppob_id"];
        $kop_id = $_POST["kop_id"];

        $mti_MessageTypeIdentification = $_POST["mti_MessageTypeIdentification"];
        $bit2_PrimaryAccountNumber = $_POST["bit2_PrimaryAccountNumber"];
        $bit3_ProcessingCode = $_POST["bit3_ProcessingCode"];
        $bit4_TransactionAmount = $_POST["bit4_TransactionAmount"];
        $bit7_TransmissionDateTime = $_POST["bit7_TransmissionDateTime"];
        $bit11_STAN = $_POST["bit11_STAN"];
        $bit12_LocalTransactionTime = $_POST["bit12_LocalTransactionTime"];
        $bit13_LocalTransactionDate = $_POST["bit13_LocalTransactionDate"];
        $bit15_SettlementDate = $_POST["bit15_SettlementDate"];
        $bit18_MerchantType = $_POST["bit18_MerchantType"];
        $bit32_AcquiringInstitutionIdentificationCode = $_POST["bit32_AcquiringInstitutionIdentificationCode"];
        $bit37_RetrievalReferenceNumber = $_POST["bit37_RetrievalReferenceNumber"];
        //$bit39_ResponseCode=$_POST["bit39_ResponseCode"];
        $bit41_TerminalID = $_POST["bit41_TerminalID"];
        $bit42_AcceptorIdentification = $_POST["bit42_AcceptorIdentification"];
        $bit48_AdditionalDataPrivate = $_POST["bit48_AdditionalDataPrivate"];
        $bit49_TransactionCurrencyCode = $_POST["bit49_TransactionCurrencyCode"];
        $bit63_DataLoket = $_POST["bit63_DataLoket"];

        $body = '';
        $body = array(
            "mti" =>  $mti_MessageTypeIdentification,
            "bit2" =>  $bit2_PrimaryAccountNumber,
            "bit3" =>  $bit3_ProcessingCode,
            "bit4" =>  $bit4_TransactionAmount,
            "bit7" =>  $bit7_TransmissionDateTime,
            "bit11" =>  $bit11_STAN,
            "bit12" =>  $bit12_LocalTransactionTime,
            "bit13" =>  $bit13_LocalTransactionDate,
            "bit15" =>  $bit15_SettlementDate,
            "bit18" =>  $bit18_MerchantType,
            "bit32" =>  $bit32_AcquiringInstitutionIdentificationCode,
            "bit37" =>  $bit37_RetrievalReferenceNumber,
            "bit41" =>  $bit41_TerminalID,
            "bit42" =>  $bit42_AcceptorIdentification,
            "bit48" =>  $bit48_AdditionalDataPrivate,
            "bit49" =>  $bit49_TransactionCurrencyCode,
            "bit61" => "",
            "bit62" => "",
            "bit63" => $bit63_DataLoket
        );
        //var_dump($body);

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $host . 'multiBillerPrePaid.php',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            )
        );
        $response = curl_exec($curl);

        $res = json_decode($response, true);
        curl_close($curl);
        return $res;
    }

    public function felloWalletSukses(Request $request, $cmd, $host)
    {
        $body = $_POST;

        return "oke";

    }
}
