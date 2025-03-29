<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
You can easily learn how to create REST API with Passport Authentication in laravel 11 application. We will learn from scratch about APIs, REST APIs, and Laravel Passport, and create an API example.

## What is API?

- An API (application programming interface) is simply a way of communication between two or more computer programs.

- APIs are also used for web and mobile application development; therefore, building a REST API is very essential for any web and mobile application developer.

# What is Laravel Passport?

- Laravel Passport is a tool for adding secure authentication to web applications. It helps developers set up authentication using APIs quickly and easily.
- Passport generates API tokens that users can use to access protected resources. It simplifies tasks like user registration, login, and managing access permissions. With Passport, developers can focus more on building their applications and less on handling authentication details.

- We will use Laravel Passport, an authentication system package for developing simple APIs for SPAs (single-page applications) which are commonly built via React JS, Angular, or Vue JS.

I m Going to install the Laravel 11 application. Then, we will install the Passport composer package for API authentication. After that, we will create register and login APIs for user authentication. 
- Then, we will create a products REST API, and you must authenticate using a user token. So, let's follow the steps

- You can clone or download the zipfile after that extracted and paste the pest the folder you want.
- Make the .env file through .env.example
- Run the following commands

````
composer update
````
````
php artisan migrate
````
````
php artisan serve
````

**If you want to install then follow some steps which your help to understand the laravel implementation process**
- Run command and get clean fresh laravel new application.
</p>

<p align="center">

- ✅ Step 1: Install Laravel 11
- ✅ Step 2: Install Passport
- ✅ Step 3: Passport Configuration
- ✅ Step 4: Create User Controller Files
- ✅ Step 5: Create Product Controller, Model And table miration
- ✅ Step 6: Create API Routes
- ✅ Step 7: Create Eloquent API Resources
- ✅ Run Laravel App

</p>

- ✅ Steps 1 First of all, we need to get a fresh Laravel 11 version application using the command below because we are starting from scratch. So, open your terminal or command prompt and run the command:

````
composer create-project "laravel/laravel:^11.0" rest-api-laravel-11
````
````
cd rest-api-laravel-11
````

- Setup the <b>.env file</b>

````
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lara_rest_api_possport
DB_USERNAME=root
DB_PASSWORD=
````

- ✅ Step 2, In Laravel 11, by default, we don't have an <b>api.php</b> route file. So, you just need to run the following command to install passport with api.php file.

````
php artisan install:api --passport
````

````
php artisan passport:client --personal
````

- ✅ Step3, In this step, we have to configure three places: <b>the model, the service provider, and the auth config file</b>. So, you just need to follow the changes in those files.

- In the model, we added the HasApiTokens class of Passport.
- In the auth.php file, we added API auth configuration.
- app/Models/User.php

````
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
````
- config/auth.php

````
<?php

return [
    
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ],
    
]

````


- ✅ step 4, In this step, we need to created a new controller called BaseController, ProductController, and UserController. I created a new folder named "API" in the Controllers folder because we'll have separate controllers for APIs. So, let's create both controllers:

````
php artisan make:controller API/BaseController
````
- app/Http/Controllers/API/BaseController.php

````
<?php
 
namespace App\Http\Controllers\API;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
 
class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
 
        return response()->json($response, 200);
    }
 
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
 
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
 
        return response()->json($response, $code);
    }
}

````

- app/Http/Controllers/API/UserController.php

````
php artisan make:controller API/UserController
````

````
<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
     
class UserController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
     
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
     
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
}
````

- ✅ step 5, Now we need to create a migration for the posts table using the Laravel 11 php artisan command, so first, fire the command

````
php artisan make:migration create_products_table
````

- After this command, you will find one file in the following path database/migrations, and you have to put the below code in your migration file to create the products table.
- database\migrations\2025_03_29_060416_create_products_table.php

````
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('detail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

````
- After changing the migration file "products" table, you need to change the Product model file So, first got tothe file in this path <b>app/Models/Product.php</b> and put the following content in it:

````
php artisan make:model Pdoduct
````
<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class Product extends Model
{
    use HasFactory;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'detail'
    ];
}

````

- After changing migration and model file we need to run above migration by following command:

````
php artisan migrate
````

- app/Http/Controllers/API/ProductController.php

````
php artisan make:controller API/ProductController
````

````
<?php
       
namespace App\Http\Controllers\API;
       
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;
use Validator;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
       
class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $products = Product::all();
        
        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
       
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);
       
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
       
        $product = Product::create($input);
       
        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    } 
     
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);
      
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
       
        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }
      
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $input = $request->all();
       
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);
       
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
       
        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->save();
       
        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }
     
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
       
        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
````


- ✅ step 6, In this step, we will create API routes. Laravel provides the api.php file for writing web service routes. So, let's add a new route to that file.
- routes/api.php

````

<?php
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
 
  
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
     
Route::middleware('auth:api')->group( function () {
    Route::resource('products', ProductController::class);
});

````

- ✅ step 6, This is a very important step in creating a REST API in Laravel 11. You can use Eloquent API resources with the API. It will help you to maintain the same response layout of your model object. 
- We used it in the ProductController file. Now, we have to create it using the following command:

````
php artisan make:resource ProductResource
````

- Now there created new file with new folder on following path:<b>app/Http/Resources/ProductResource.php</b>

````
<?php
  
namespace App\Http\Resources;
  
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
  
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'detail' => $this->detail,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}

````
- All the required steps have been done, now you have to type the given below command and hit enter to run the Laravel app:

````
php artisan serve
````
````
'headers' => [
    'Accept' => 'application/json',
    'Authorization' => 'Bearer '.$accessToken,
]
````
- Here is Routes URL, Now simply you can run above listed url like as bellow screen shot:
- Register POST Method Api

````
http://localhost:8000/api/register
````
- Login POST Method Api

````
http://localhost:8000/api/login
````
- Use GET Method to view all the products
- Use POST Method to add product

````
http://localhost:8000/api/products
````
- Use GET Method to view single the products
- Use PUT Method to update single the products
- Use DELETE Method to Delete single the products
````
http://localhost:8000/api/products/id
````

- ✅ Thanks For watching if you Understand my explanation then support me and my youtube channel [AMK DEVELOPMENT](https://www.youtube.com/@amkdevelopment?sub_confirmation=1)

```
Email:    mohibulkhan15992@gmail.com
Contact:  +917007192298
````









