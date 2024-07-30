<?php

namespace App\Http\Controllers;

use App\Models\FoodServiceRequest;
use App\Models\Hotel;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WithdrawalController extends Controller
{
    public function nominal($hotel_id)
    {
        try {
            $nominal = FoodServiceRequest::where('hotel_id', $hotel_id)
                ->where('payment_method', 'Scan QR')
                ->where('is_paid', 1)
                ->where('is_withdrawn', null)
                ->sum('total');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get nominal',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'nominal' => $nominal
        ]);
    }

    public function request_withdrawal(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required',
            'bank' => 'required',
            'account_name' => 'required',
            'nominal' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $withdrawal = Withdrawal::create([
                'hotel_id' => $hotel_id,
                'account_number' => $request->account_number,
                'bank' => $request->bank,
                'account_name' => $request->account_name,
                'nominal' => $request->nominal,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to create withdrawal request',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'id' => $withdrawal->id,
            'account_number' => $request->account_number,
            'bank' => $request->bank,
            'account_name' => $request->account_name,
            'nominal' => $request->nominal,
        ]);
    }

    public function withdrawal_request_history($hotel_id)
    {
        $withdrawals = Withdrawal::where('hotel_id', $hotel_id)->get();

        try {
            $withdrawal_history = [];

            foreach ($withdrawals as $withdrawal) {
                $date = $withdrawal->created_at;
                $nominal = $withdrawal->nominal;
                $account_number = $withdrawal->account_number;
                $bank = $withdrawal->bank;
                $account_name = $withdrawal->account_name;
                $status = $withdrawal->status;
                $payment_receipt = $withdrawal->payment_receipt;

                $withdrawal_history[] = [
                    'id' => $withdrawal->id,
                    'date' => $date->format('Y-m-d H:i'),
                    'nominal' => $nominal,
                    'account_number' => $account_number,
                    'bank' => $bank,
                    'account_name' => $account_name,
                    'status' => $status,
                    'payment_receipt' => $payment_receipt,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get withdrawal request history',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'withdrawal_history' => $withdrawal_history,
        ]);
    }

    public function withdrawal_request_list()
    {
        try {
            $withdrawals = Withdrawal::all();

            $withdrawal_list = [];

            foreach ($withdrawals as $withdrawal) {
                $hotel_name = Hotel::where('id', $withdrawal->hotel_id)->value('name');
                $account_number = $withdrawal->account_number;
                $account_name = $withdrawal->account_name;
                $nominal = $withdrawal->nominal;

                $withdrawal_list[] = [
                    'id' => $withdrawal->id,
                    'hotel_name' => $hotel_name,
                    'account_number' => $account_number,
                    'account_name' => $account_name,
                    'nominal' => $nominal,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get withdrawal request list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'withdrawal_list' => $withdrawal_list,
        ]);
    }

    public function upload_payment_receipt(Request $request, $withdrawal_id)
    {
        $validator = Validator::make($request->all(), [
            'payment_receipt' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $withdrawal = Withdrawal::where('id', $withdrawal_id)->first();

            $file_name = time() . " - " . $request->payment_receipt->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_payment_receipt = asset("uploads/payment_receipt/" . $file_name);
            $request->payment_receipt->move(public_path('uploads/payment_receipt/'), $file_name);

            $withdrawal->update([
                'payment_receipt' => $path_payment_receipt,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save hotel photo',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'path_payment_receipt' => $path_payment_receipt
        ]);
    }
}
