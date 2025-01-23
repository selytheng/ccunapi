<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\FileUploadController;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function create(Request $req)
    {
        try {
            $validator = $req->validate([
                'title' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
                'partner_id' => 'required|integer|exists:partners,id',
                'location' => 'required|string',
                'status' => 'required|in:active,inactive',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);

            // Handle main image upload
            $image = FileUploadController::storeImage($req->file('image'), 'uploads/events');
            $validator['image'] = $image;

            // Handle gallery images upload
            $gallery = [];
            if ($req->hasFile('gallery')) {
                foreach ($req->file('gallery') as $file) {
                    $galleryImage = FileUploadController::storeImage($file, 'uploads/events/gallery');
                    $gallery[] = $galleryImage;
                }
            }
            $validator['gallery'] = $gallery;

            // Create event with timezone conversion
            $event = Event::create(array_merge($validator, [
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
        $title = $req->input('title');
        if ($title) {
            $events = Event::where('title', 'like', '%' . $title . '%')->get();
        } else {
            $events = Event::all();
        }
        return response()->json($events, Response::HTTP_OK);
    }

    public function getById($id)
    {
        $event = Event::with('partner')->find($id);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($event, Response::HTTP_OK);
    }

    public function update(Request $req, $id)
    {
        try {
            $validator = $req->validate([
                'title' => 'string',
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'string',
                'partner_id' => 'integer|exists:partners,id',
                'location' => 'string',
                'status' => 'in:active,inactive',
                'start_date' => 'date',
                'end_date' => 'date|after:start_date',
                'remove_gallery' => 'array' // Array of gallery image paths to remove
            ]);

            $event = Event::find($id);
            if (!$event) {
                return response()->json(['message' => 'Event not found'], Response::HTTP_NOT_FOUND);
            }

            // Handle main image upload if present
            if ($req->hasFile('image')) {
                $image = FileUploadController::storeImage($req->file('image'), 'uploads/events');
                $validator['image'] = $image;
            }

            // Handle gallery updates
            $gallery = $event->gallery ?? [];

            // Remove specified images from gallery
            if (isset($validator['remove_gallery'])) {
                $gallery = array_diff($gallery, $validator['remove_gallery']);
                unset($validator['remove_gallery']);
            }

            // Add new gallery images
            if ($req->hasFile('gallery')) {
                foreach ($req->file('gallery') as $file) {
                    $galleryImage = FileUploadController::storeImage($file, 'uploads/events/gallery');
                    $gallery[] = $galleryImage;
                }
            }
            $validator['gallery'] = array_values($gallery); // Reindex array

            $event->update(array_merge($validator, [
                'updated_at' => Carbon::now('Asia/Phnom_Penh'),
            ]));

            return response()->json($event, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    public function delete($id)
    {
        try {
            $event = Event::find($id);
            if (!$event) {
                return response()->json(['message' => 'Event not found'], Response::HTTP_NOT_FOUND);
            }
            $event->delete();
            return response()->json(['message' => 'Event deleted successfully.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    protected function handleValidationException(ValidationException $e)
    {
        return response()->json(
            [
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    protected function handleUnexpectedException(\Exception $e)
    {
        Log::error('Unexpected error occurred', ['exception' => $e]);
        return response()->json(
            [
                'error' => 'An unexpected error occurred!!'
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
