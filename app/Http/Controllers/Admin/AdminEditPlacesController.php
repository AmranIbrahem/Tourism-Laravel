<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddAreaRequest;
use App\Http\Requests\Admin\AddCityRequest;
use App\Http\Requests\Admin\AddCountryRequest;
use App\Http\Requests\Admin\EditAreaRequest;
use App\Http\Responses\Response;
use App\Models\Places\Area;
use App\Models\Places\City;
use App\Models\Places\Country;
use App\Models\Places\PicturesArea;
use Illuminate\Http\Request;

class AdminEditPlacesController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Country :
    public function AddCountry(AddCountryRequest $request){
        $country = Country::create([
            "CountryName" => $request->CountryName,
        ]);

        if ($country) {
            return Response::Message("Added $request->CountryName successfully",200);
        } else {
            return Response::Message("Added failed..!",401);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Edit Country :
    public function EditCountry(AddCountryRequest $request, $idCountry ){
        $country = Country::Find($idCountry);

        if ($country) {
            $country->CountryName=$request->CountryName;
            $country->save();

            return Response::Message("Edited $request->CountryName successfully",200);
        } else {
            return Response::Message("Something is wrong..!",401);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Delete Country :

    public function DeleteCountry( $idCountry ){
        $country = Country::Find($idCountry);

        if ($country) {
            $CountryName=$country->CountryName;
            $country->delete();

            return Response::Message("Edited $CountryName successfully",200);
        } else {
            return Response::Message("Something is wrong..!",401);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Show Country :
        public function ShowCountry( ){
            $country = Country::all();

            if (count($country) >0) {
                return Response::Message($country,200);
            } else {
                return Response::Message("No countries to display ....!",401);
            }
        }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add City :
    public function AddCity(AddCityRequest $request , $idCountry){
        $country = Country::find($idCountry);
        if ($country) {
            $city = City::create([
                "CityName" => $request->CityName,
                "country_id"=>$idCountry
            ]);
            if($city){
                return Response::Message("Added $request->CityName to $country->CountryName successfully",200);
            }else{
                return Response::Message("Something is wrong..!",401);
            }

        } else {
            return Response::Message("Not Found Country..!",401);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Edit City :
    public function EditCity(AddCityRequest $request, $idCountry ){
        $city = City::Find($idCountry);

        if ($city) {
            $city->CityName=$request->CityName;
            $city->save();

            return Response::Message("Edited $request->CityName successfully",200);
        } else {
            return Response::Message("Something is wrong..!",401);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Delete City :
    public function DeleteCity( $idCity ){
        $city = City::Find($idCity);

        if ($city) {
            $CityName=$city->CityName;
            $city->delete();

            return Response::Message("Edited $CityName successfully",200);
        } else {
            return Response::Message("Something is wrong..!",401);
        }
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Area :
    public function AddArea(AddAreaRequest $request , $nameCity){
        $city = City::where('CityName', $nameCity)->first();
        if ($city) {
            $area = Area::create([
                "AreaName" => $request->AreaName,
                "Details" => $request->Details,
                "city_id" => $city->id
            ]);
            if ($area) {
                return Response::Message("Added $request->AreaName to $nameCity successfully", 200);
            } else {
                return Response::Message("Something is wrong..!", 401);
            }
        } else {
            return Response::Message("Not Found City..!", 404);
        }
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// edit Area :
    public function editArea(EditAreaRequest $request , $idArea){
        $area = Area::find( $idArea);
        if ($area) {
            if($request->AreaName){
                $area->AreaName=$request->AreaName;
            }
            if($request->Details){
                $area->Details=$request->Details;
            }
            $save=$area->save();
            if($save){
                return Response::Message("Edited successfully",200);
            }else{
                return Response::Message("Something is wrong..!",401);
            }

        } else {
            return Response::Message("Not Found Area..!",404);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// delete Area :
    public function deleteArea($idArea){
        $area = Area::find( $idArea);
        if ($area) {
            $delete=$area->delete();
            if($delete){
                return Response::Message("Delete successfully",200);
            }else{
                return Response::Message("Something is wrong..!",401);
            }

        } else {
            return Response::Message("Area not found..!",404);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Photo to Area :

    public function addPhotosToArea(Request $request, $area_id)
    {
        $area = Area::find($area_id);
        if (!$area) {
            return Response::Message("Area not found..!",404);
        }
        if (!$request->hasFile('photos')) {
            return Response::Message("No photos uploaded",400);
        }

        $photos = $request->file('photos');
        $photoPaths = [];

        foreach ($photos as $photo) {
            $destination = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('area_photos'), $destination);

            $photoPath = "area_photos/$destination";

            PicturesArea::create([
                'area_id' => $area_id,
                'photo' => $photoPath
            ]);

            $photoPaths[] = $photoPath;
        }

        return response()->json([
            'message' => 'Photos added successfully',
            'photos' => $photoPaths
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Show Photo Area :
    public function showPhotosArea($area_id)
    {
        $area = Area::with('pictures')->find($area_id);

        if ($area) {
            if ($area->pictures->count() > 0) {
                return response()->json(['photos' => $area->pictures], 200);
            } else {
                return Response::Message("No photos found for this area.",404);
            }
        }
        return Response::Message("Not Found Area..!",404);

    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// delete Photo Area :
    public function deletePhotoFromArea($photo_id)
    {
        $picture = PicturesArea::find($photo_id);

        if ($picture) {
            $photoPath = public_path('' . $picture->photo);

            if (file_exists($photoPath)) {
                unlink($photoPath);
            } else {
                return Response::Message("Photo not found in storage!", 404);
            }

            $picture->delete();

            return Response::Message("Photo deleted successfully!", 200);
        } else {
            return Response::Message("Photo not found in database!", 404);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Show Area :
    public function getAreaDetails($id)
    {
        $area = Area::with(['city' => function ($query) {
            $query->select('id', 'CityName');
        }, 'pictures'])->find($id);

        if ($area) {
            return response()->json([
                'message' => 'Area details retrieved successfully!',
                'data' => [
                    'id' => $area->id,
                    'AreaName' => $area->AreaName,
                    'Details' => $area->Details,
                    'Rate' => $area->Rate,
                    'CityName' => $area->city->CityName ?? null,
                    'pictures' => $area->pictures ?? [],
                ],
            ], 200);
        } else {
            return Response::Message("Area not found!", 404);
        }
    }




}


