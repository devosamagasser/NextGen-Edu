<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SwaggerController extends Controller
{
    /**
     *
     * @OA\Server(
     *     url="http://127.0.0.1:8000",
     *     description="HTTP Server"
     * )
     *
     * @OA\SecurityScheme(
     *     securityScheme="Bearer",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     name="Authorization",
     *     in="header",
     * )
     *
     * @OA\Info(
     *     title="Chief-Mate",
     *     version="1.0.0",
     *     description="API documentation for N-G-E Application",
     *     @OA\Contact(
     *         name="Osama Gasser",
     *         email="devosamagasser@gmail.com"
     *     ),
     *     @OA\License(
     *         name="Developed by Osama Gasser",
     *         url="https://example.com"
     *     )
     * )
     *
     * @OA\Schema(
     *     schema="User",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *     @OA\Property(property="phone", type="string", example="099 2899 634 34"),
     *     @OA\Property(property="avatar", type="string", example="avatars/avatar.jpg"),
     * )
     *
     * @OA\Schema(
     *     schema="Workspace",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Workspace name"),
     *     @OA\Property(property="color", type="string", example="blue"),
     *     @OA\Property(property="color_code", type="string", example="#0000FF"),
     * )
     *
     * @OA\Schema(
     *     schema="Categories",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="title", type="string", example="category title"),
     *     @OA\Property(property="worksapce_id", type="integer", example="2"),
     * )
     *
     * @OA\Schema(
     *     schema="Sections",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="title", type="string", example="section title"),
     *     @OA\Property(property="worksapce_id", type="integer", example="2"),
     * )
     *
     * @OA\Schema(
     *     schema="Warehouse",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="title", type="string", example="section title"),
     *     @OA\Property(property="type", type="integer", example="equipment || ingredient"),
     * )
     *
     * @OA\Schema(
     *     schema="Ingredients",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="ingriedent name"),
     *     @OA\Property(property="cover", type="sting", example="cover.jpg"),
     *     @OA\Property(property="description", type="sting", example="some description to descripe ingredient"),
     *     @OA\Property(property="unit", type="sting", example="ml || l || gm || kg || unit"),
     *     @OA\Property(property="quantity", type="integer", example="12.5 || 11"),
     * )
     *
     * @OA\Schema(
     *     schema="Equipments",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="ingriedent name"),
     *     @OA\Property(property="cover", type="sting", example="cover.jpg"),
     *     @OA\Property(property="description", type="sting", example="some description to descripe equipment"),
     *     @OA\Property(property="unit", type="sting", example="unit"),
     *     @OA\Property(property="quantity", type="integer", example="11"),
     * )
     *
     */
    public function index(){

    }
}
