<?php

namespace App\Services;
use App\Models\User;
use Request;

class CheckoutService
{
    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    private function serializeArray($array) {
        $ret_value = "";
    
        foreach ($array as $key => $val) {
            /* skip signature hashes from computed hash */
            if (in_array($key , ['HASH', 'SIGNATURE_SHA2_256', 'SIGNATURE_SHA3_256'], true)) {
                continue;
            }
            //serialize array to length + element for all elements 
            if (is_array($val)) {
                $ret_value .= $this->serializeArray($val);
            } else {
                $ret_value .= strlen($val) . $val;
            }
        }
    
        return $ret_value;
    }

    private function verifyHash(string $stringForHash, string $recievedHash, string $secretKey){
        $computedHash = hash_hmac('sha3-256' , $stringForHash, $secretKey);
        if($computedHash == $recievedHash)
        return true;
        else return false;
    }
    public function handleSubscriptionEvent(Request $request)
    {
        $secretKey = config('checkout.secret_key');
        $verified = $this->verifyHash($this->serializeArray($request->all()), $request->input('SIGNATURE_SHA3_256'), $secretKey);
        if($verified){
            $upgraded = $this->upgradeUser($request->input("EMAIL"));
            $responseDate = date("YmdGis");
            $stringForHash = $this->serializeArray(['license' => $request->input('LICENSE_CODE'), 'expDate' => $request->input('EXPIRATION_DATE'), 'Date'=>$responseDate]);
            $hashed = hash_hmac('sha3-256' , $stringForHash, $secretKey);
            //Send confirmation event was received, per checkout docs
            return '<sig algo="sha3-256" date="'.$responseDate.'">'.$hashed.'</sig>';
        }
        else return 400;
    }
    private function upgradeUser($user_email){
        $user = $this->user::query()->where('email', '=', $user_email)->first();
        if($user){
            error_log('found user');
            $user->upgraded = true;
            $user->save();
            return true;
        }else{
            return false;
            error_log('user not found');
        }

    }
}
