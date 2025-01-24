<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\FileUploadController;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class TrainingController extends Controller
{
    public function create(Request $req)
    {
        try {
            $validator = $req->validate([ 
                'name'          => 'required|string',
                'major_id'   => 'required|integer|exists:majors,id',
                'year_id'   => 'required|integer|exists:years,id',
                'image'         => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description'     => 'required|string',
                'link'     => 'required|string',
            ]);

            // Handle image upload
            $image = FileUploadController::storeImage($req->file('image'), 'uploads/events');
            $validator['image'] = $image;

            // Create product with timezone conversion
            $event = Training::create(array_merge($validator, [
                'created_at' => Carbon::now('Asia/Phnom_Penh'),
                'updated_at' => Carbon::now('Asia/Phnom_Penh'),
            ]));

            return response()->json($event, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    public function get(Request $req)
    {
        // validate name as an input to search the product
        $name = $req->input('name');
        if ($name) {    // search product
            $products = Training::where('name', 'like', '%' . $name . '%')->get();
            return response()->json($products, Response::HTTP_OK);
        } else {        //get all product
            $products = Training::all();
            return response()->json($products, Response::HTTP_OK);
        }
    }
    public function getAllEvents()
    {
        try {
            // Fetch all courses with their associated partner_id
            $events = Training::with('major.partner')->get()->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'description' => $event->description,
                    'image' => $event->image,
                    'link' => $event->link,
                    'major_id' => $event->major_id,
                    'year_id' => $event->year_id,
                    'partner_id' => $event->major->partner->id ?? null, // Use null if no partner is found
                ];
            });

            return response()->json($events, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }


    public function getById($id)
    {
        $produtcs = Training::find($id);
        return response()->json($produtcs, Response::HTTP_OK);
    }

    public function update(Request $req, $id)
    {
        try {
            $validator = $req->validate([
                'name'          => 'required|string',
                'major_id'      => 'integer|exists:majors,id',
                'year_id'       => 'integer|exists:years,id',
                'image'         => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'description'   => 'nullable|string',  // Changed to nullable
                'link'           => 'nullable|string',
            ]);

            $updateEvent= Training::find($id);
            if (!$updateEvent) {
                return response()->json(['message' => 'Event not found'], Response::HTTP_NOT_FOUND);
            }

            // Handle image upload if present
            if ($req->hasFile('image')) {
                $image = FileUploadController::storeImage($req->file('image'), 'uploads/courses');
                $validator['image'] = $image; // Add the image path to the data
            }

            // Update the course with validated data and handle the update process
            $updateEvent->update(array_merge($validator, [
                'updated_at' => Carbon::now('Asia/Phnom_Penh'),
            ]));

            return response()->json($updateEvent, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }


    public function delete($id)
    {
        try {
            $deleteEvent= Training::find($id);
            if (!$deleteEvent) {
                return response()->json(['message' => 'Event not found'], Response::HTTP_NOT_FOUND);
            }
            $deleteEvent->delete();
            return response()->json(['message' => 'Event deleted successfull.'], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    protected function handleValidationException(ValidationException $e)
    {
        return response()->json(
            [
                'message'   => 'Validation Error',
                'errors'    => $e->errors()
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    protected function handleUnexpectedException(\Exception $e)
    {
        Log::error('Unexpected error occurred', ['exception' => $e]);

        return response()->json(
            [
                'error' => 'An unexpected error occurred.'
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
