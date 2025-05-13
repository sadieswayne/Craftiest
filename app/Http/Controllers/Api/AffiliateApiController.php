<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateActivity;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserAffiliate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateApiController extends Controller
{
    /**
     * Get affiliate info
     *
     * @OA\Get(
     *      path="/api/affiliates/",
     *      operationId="affiliates",
     *      tags={"Affiliates"},
     *      summary="Get affiliate totals of current user",
     *      description="Get affiliate earnings and withdrawal totals of current user.",
     *      security={{ "passport": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     */
    public function affiliates(Request $request)
    {
        $user = Auth::user();
        $list = $user->affiliates;
        $list2 = $user->withdrawals;
        $totalEarnings = 0;
        foreach ($list as $affOrders) {
            $totalEarnings += $affOrders->orders->sum('affiliate_earnings');
        }
        $totalWithdrawal = 0;
        foreach ($list2 as $affWithdrawal) {
            $totalWithdrawal += $affWithdrawal->amount;
        }

        $earnings = $totalEarnings - $totalWithdrawal;
        if ($earnings < 0) {
            $earnings = 0;
        }

        // Get currency
        $currencyId = Setting::getCache()->default_currency ?? 124;
        $currency = Currency::find($currencyId);

        $affilate_code = $user->affiliate_code;

        return response()->json([
            'earnings'         => $earnings,
            'total_earnings'   => $totalEarnings,
            'total_withdrawal' => $totalWithdrawal,
            'affiliate_code'   => $affilate_code,
            'currency'         => $currency,
        ], 200);
    }

    /**
     * Get withdrawals info
     *
     * @OA\Get(
     *      path="/api/affiliates/withdrawals",
     *      operationId="withdrawals",
     *      tags={"Affiliates"},
     *      summary="Get withdrawals of current user",
     *      description="Get affiliate withdrawals list of current user.",
     *      security={{ "passport": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     */
    public function withdrawals(Request $request)
    {
        $user = Auth::user();
        $data = UserAffiliate::where('user_id', $user->id)
            ->orderByRaw("CASE WHEN status = 'Waiting' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($data);
    }

    /**
     * Request withdrawal
     *
     * @OA\Post(
     *      path="/api/affiliates/request-withdrawal",
     *      operationId="requestWithdrawal",
     *      tags={"Affiliates"},
     *      summary="Request withdrawal for current user",
     *      description="Request withdrawal for current user",
     *      security={{ "passport": {} }},
     *
     *      @OA\RequestBody(
     *         required=true,
     *         description="Request withdrawal data",
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *                 @OA\Property(
     *                     property="affiliate_bank_account",
     *                     description="Bank Account info",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     description="Amount to withdraw",
     *                     type="float"
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
     */
    public function requestWithdrawal(Request $request)
    {

        if ($request->affiliate_bank_account == null) {
            return response()->json(['error' => __('Bank Account missing.')], 412);
        }
        if ($request->amount == null) {
            return response()->json(['error' => __('Amount missing.')], 412);
        }

        $user = Auth::user();
        $list = $user->affiliates;
        $list2 = $user->withdrawals;

        $totalEarnings = 0;
        foreach ($list as $affOrders) {
            $totalEarnings += $affOrders->orders->sum('affiliate_earnings');
        }
        $totalWithdrawal = 0;
        foreach ($list2 as $affWithdrawal) {
            $totalWithdrawal += $affWithdrawal->amount;
        }
        if ($totalEarnings - $totalWithdrawal >= $request->amount) {
            $user->affiliate_bank_account = $request->affiliate_bank_account;
            $user->save();
            $withdrawalReq = new UserAffiliate;
            $withdrawalReq->user_id = Auth::id();
            $withdrawalReq->amount = $request->amount;
            $withdrawalReq->save();

            CreateActivity::for($user, 'Sent', 'Affiliate Withdraw Request');

            return response()->json(['message' => 'Affiliate Withdrawal Requested'], 200);
        } else {
            return response()->json(['error' => __('Not enough earnings')], 412);
        }

    }
}
