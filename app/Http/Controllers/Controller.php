<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function contactUs(Request $request): JsonResponse
    {
        if( $request->filled('name') &&
            $request->filled('email') &&
            $request->filled('phone') &&
            $request->has('country') &&
            $request->filled('formMessage') )
        {

            if ( !preg_match("|^([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is", strtolower($request->email)) ) {
                return response()->json(['estatus' => 1, 'message' => "Error al enviar. Dirección de correo no válida."]);
            }

//            $rules = array(
//                'g-recaptcha-response'=>'required|recaptcha',
//            );
//
//            $data = array('g-recaptcha-response' => $request->input('g-recaptcha-response'));
//
//            $validator = Validator::make($data, $rules);
//
//            if ($validator->fails()) {
//                return response()->json(['estatus' => 0,
//                    'message' => 'No se envió el mensaje. Favor comprobar marcar el campo "No soy un Robot".']);
//            }

            $data = array(
                'name' => $request->name,
                'email'  => $request->email,
                'phone'  => $request->phone,
                'country'  => $request->country,
                'formMessage'  => $request->formMessage,
            );
            Mail::send('mail',$data, function($message) use($request){
                $message->replyTo($request->email, $request->name);
                $message->to('eugenia@bmbconsulting.com.py');
                $message->subject("Contacto desde la página web BMBConsulting");
            });

//            Mail::send('mail-bienvenida',$data, function($message) use($request){
//                $message->to($request->email);
//                $message->subject("Contacto con BMB Consulting");
//            });

            return response()->json(['estatus' => 0, 'message' => "Hemos recibido tu mensaje. Gracias por contactarnos."]);

        }else{
            //No se envía el email pero se responde como que sí se envió
            Log::info("mail failures. data incomplete");
            return response()->json(['estatus' => 0, 'message' => "Mensaje no enviado. Favor completar todos los campos."]);
        }

    }

}
