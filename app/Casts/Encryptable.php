<?php
namespace App\Casts;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class Encryptable implements CastsAttributes
{
    private $newEncrypter;

    public function __construct()
    {
        $this->newEncrypter = new Encrypter( Config::get( 'app.key-crypter' ) , Config::get( 'app.cipher' ) );
    }
    public function get($model, $key, $value, $attributes)
    {
        try{
            return $value === null ? null : $this->newEncrypter->decryptString($value);
        }
        catch(\Exception $e){
            Log::alert($e->getMessage());
            return $value;
        }
    }

    public function set($model, $key, $value, $attributes)
    {
        try{
            return $value === null ? null : $this->newEncrypter->encryptString($value);
        }catch (\Exception $e){
            Log::alert($e->getMessage());
            return $value;
        }
    }
}
