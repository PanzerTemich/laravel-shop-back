<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // $products = App\Product::all();
    // foreach ($products as $product) {
    //     echo $product->PRICE . " ";
    // }
    return view('welcome');
    // $products = DB::select('select * from products');
    // foreach ($products as $product) {
        // echo $products->PRICE . " ";
        // echo $products[2]->PRICE;
    // }
});
Route::get('/products', function(){
    // return response()->json([
    //     'name' => 'Yamileth',
    //     'edad' => 24
    // ]);
    //Below I look for all products. I set App\Models\ up to the top and then Products::all(); down here. 
    //Aquí abajo busco todos los productos. Se puede poner App\Models\ hasta arriba y luego sólo Products::all(); aquí abajo
    $products=App\Models\Product::all();
    //Sending data through the $productos variable which is an array
    //Envio los datos a traves de la variable $productos la cual es un array
    return response()->json($products);
    // return response()->json([
    //     'name' => $users->name
    // ]);
});
Route::post('/register',function(Request $req){
    $retriveUser=User::where('email',$req->input('email'))->first();
    if($retriveUser){
        return response()->json([
            'error'=>'El Usuario ya existe'
        ]);
    }
    $user = new User();
    $user->email = $req->input('email');
    $user->nombre = $req->input('name');
    $user->apellido = $req->input('lastname');
    $user->pass= Crypt::encryptString($req->input('password'));
    // $user->pass= $req->input('password');
    $session=(string)rand(100000,10000000);
    $user->sessions=$session;
    $user->save();
    //A search is made below, it is made by email
    //Aqui abajo se hace una busqueda, se busca por email
    $retrive=User::where('email', $req->input('email'))
    ->get();
    //Data is retrived from $retrive, data comes in an array
    //Recuperamos la data de $retrive, la data viene en un array.
    //As it is only a record the variable $retrive[0] is entered and the data is accessed
    //Como sólo es un registro se ingresa a $retrive[0] y se accede a los datos
    return response()->json([
        'email'=> $retrive[0]->email,
        'name'=> $retrive[0]->nombre,
        'lastname'=>$retrive[0]->apellido,
        'cart'=>$retrive[0]->cart,
        'session'=>intval($session)
    ]);
    // return response()->json($retrive);
    // return redirect('http://localhost:4200/login');
});
Route::post('/logged',function(Request $req){
    $retrive=User::where('email',$req->input('email'))
    ->get();
    $sessions=explode(",",$retrive[0]->sessions);
    foreach($sessions as $session){
        if($session===$req->input('session')){
            return response()->json([
                'cart'=>$retrive[0]->cart
            ]);
            break;
        }
    }
    return response()->json([
        'error'=>"No se ha podido conectar"
    ]);
});
Route::post('/loggin',function(Request $req){
    $retrive=User::where('email',$req->input('email'))
    ->get();
    if(count($retrive)>=1){
        if(Crypt::decryptString($retrive[0]->pass)===$req->input('password')){
           $session=(string)rand(100000,10000000);
           $retrive[0]->sessions=$retrive[0]->sessions . ',' . $session;
           //The save() method only works when an element is indicated in the array
           //El método save sólo funciona cuando se indica un elemento en el array
           $retrive[0]->save();
            return response()->json([
               'email'=> $retrive[0]->email,
               'name'=> $retrive[0]->nombre,
               'lastname'=>$retrive[0]->apellido,
               'cart'=>$retrive[0]->cart,
               'session'=>intval($session)
            ]);
        }else{
            return response()->json([
                'error'=>'Contraseña Incorrecta'
            ]);
        }
    }else{
         return response()->json([
             'error'=> 'El usuario no existe'
         ]);
     }
});
Route::post('/logout',function(Request $req){
    $retrive=User::where('email',$req->input('email'))
    ->get();
    if(count($retrive)>=1){
        $sessions=explode(',',$retrive[0]->sessions);
        for($i=0;$i<count($sessions);$i++){
            if($sessions[$i]===$req->input('session')){
                unset($sessions[$i]);
                $sessions=implode(',',$sessions);
                $retrive[0]->sessions=$sessions;
                $retrive[0]->save();
                //Test, sending some data
                //Prueba de envio de algun dato
                return response()->json([
                    'response'=>'Logout satisfactorio'
                ]);
            break;
            }
        }
    }
});
Route::post('/getcart',function (Request $req){
    $retrive=User::where('email',$req->input('email'))->first();
    if($retrive){
        return response()->json([
            'cart'=>$retrive->cart
        ]);
    }
});
Route::post('/cart',function(Request $req){
    $retrive=User::where('email',$req->input('email'))->first();
    if($retrive){
        $retrive->cart=$req->input('cart');
        $retrive->save();
        return response()->json([
            'cart'=>$retrive->cart
        ]);
    }
});
Route::post('/getaddress',function(Request $req){
    $retrive=User::where('email',$req->input('email'))->get();
    if(count($retrive)){
        return response()->json([
            'receiver'=>$retrive[0]->receiver,
            'mainstreet'=>$retrive[0]->mainStreet,
            'number'=>$retrive[0]->number,
            'secondstreet'=>$retrive[0]->secondStreet,
            'thirdstreet'=>$retrive[0]->thirdStreet,
            'zip'=>$retrive[0]->zip,
            'city'=>$retrive[0]->city,
            'state'=>$retrive[0]->state
        ]);
    }
});
Route::post('/updateaddress',function(Request $req){
    $retrive=User::where('email',$req->input('email'))->get();
    if(count($retrive)){
        $retrive[0]->receiver=$req->input('receiver');
        $retrive[0]->mainStreet=$req->input('mainstreet');
        $retrive[0]->number=$req->input('number');
        $retrive[0]->secondStreet=$req->input('secondstreet');
        $retrive[0]->thirdStreet=$req->input('thirdstreet');
        $retrive[0]->zip=$req->input('zip');
        $retrive[0]->city=$req->input('city');
        $retrive[0]->state=$req->input('state');
        $retrive[0]->save();
        return response()->json([
            'receiver'=>$retrive[0]->receiver,
            'mainstreet'=>$retrive[0]->mainStreet,
            'number'=>$retrive[0]->number,
            'secondstreet'=>$retrive[0]->secondStreet,
            'thirdstreet'=>$retrive[0]->thirdStreet,
            'zip'=>$retrive[0]->zip,
            'city'=>$retrive[0]->city,
            'state'=>$retrive[0]->state
        ]);
    }
});