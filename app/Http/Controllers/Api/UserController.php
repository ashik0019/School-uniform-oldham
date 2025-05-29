<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserCollection;
use App\Traits\ResponseAPI;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use ResponseAPI;
    public function info($id)
    {
        return new UserCollection(User::where('id', $id)->get());
    }


    public function updateName(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $request->user_id,
            ]);
            $user = User::findOrFail($request->user_id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->balance = $request->balance;
            // if ($request->hasFile('avatar_original') && $request->avatar_original !== $user->avatar_original) {
            //     $user->avatar_original = $request->avatar_original->store('uploads/users');
            // }
            // Handle Base64 image upload
            if ($request->has('avatar_original') && $request->avatar_original !== $user->avatar_original) {
                $imageData = $request->avatar_original;

                // Decode the Base64 string
                $imageParts = explode(';base64,', $imageData);
                if (count($imageParts) === 2) {
                    $imageBase64 = base64_decode($imageParts[1]);

                    // Determine the file extension
                    $mimeType = str_replace('data:image/', '', $imageParts[0]);
                    $extension = $mimeType === 'jpeg' ? 'jpg' : $mimeType;

                    // Generate a unique file name
                    $fileName = 'uploads/users/' . uniqid() . '.' . $extension;

                    // Store the image in the desired location
                    Storage::put($fileName, $imageBase64);

                    // Save the file path in the database
                    $user->avatar_original = $fileName;
                }
            }

            $user->save();
            return $this->success('Profile information has been updated successfully', $user, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', 422);
        } catch (\Exception $e) {
            return $this->error('Failed to update profile: ' . $e->getMessage(), 500);
        }
    }
}
