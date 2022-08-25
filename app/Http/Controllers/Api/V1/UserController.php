<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Languages;
use App\model\Devicedata;
use App\model\userDetails;
use DB;
use App\model\friendList;
use App\model\Setting;
use App\model\UserSubscriptions;
use App;
//use App\Repositories\PushNotificationRepository;
use Edujugon\PushNotification\PushNotification;

class UserController extends Controller
{

    /* public function __construct(PushNotificationRepository $pushNotRep)
    {
        $this->PushNotificationRepository = $pushNotRep;
    } */

    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] phone_number
     */
    public function signup(Request $request)
    {
        $input =
            $request->all();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'device_type' => 'required|integer',
            'device_id' => 'required|string',
            'device_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            $ret = array('success' => 0, 'message' => $validator->messages()->first());
            return json_encode($ret);
        }

        $languages = Languages::select('id', 'country', 'country_code', 'flag')->get();
        // $languages = Languages::select('id', 'country', 'country_code')->get();

        $user = new User([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'language'  => 'en',
            'credits' => 5,
            // 'phone_number' => $request->phone_number,
        ]);
        $user->save();
        $insertedId = $user->id;
        if ($insertedId) {
            $inputArr = [
                'user_id'       => $insertedId,
                'device_type'   => $input['device_type'],
                'device_id'     => $input['device_id'],
                'device_token'  => $input['device_token']
            ];

            $result = DeviceData::create($inputArr);

            $details = userDetails::create(['user_id'   =>  $insertedId]);

            if ($result->id) {
                $activity = User::where('id', $insertedId)->first()->toArray();
                $userD = userDetails::where('user_id', $insertedId)->first();
                if ($userD) {
                    $user_D = $userD->toArray();
                    $commonData = array_merge($activity, $user_D);
                    unset($commonData['id']);
                    $commonData['id'] = $commonData['user_id'];
                } else {
                    $commonData = $activity;
                }

                if ($commonData['real_name'] == '' || $commonData['app_seen_name'] == '' || $commonData['about_me'] == '' || ($commonData['whatsapp'] == '' && $commonData['instagram'] == '' && $commonData['snapchat'] == '' && $commonData['twitter'] == '' && $commonData['skype'] == '' && $commonData['linkedin'] == '')) {
                    $isProfileUpdated = 0;
                    $commonData['isProfileUpdated'] = $isProfileUpdated;
                } else {
                    $isProfileUpdated = 1;
                    $commonData['isProfileUpdated'] = $isProfileUpdated;
                }
                //return json_encode(array('method' => 'signup', 'message' => 'User registration successfully.', 'success' => 1, 'userId' => $insertedId, 'is_available' => $activity->is_available));
                return json_encode(array('method' => 'signup', 'message' => 'User registration successfully.', 'success' => 1, 'user_data' => $commonData, 'languages' => $languages));
            } else {
                return json_encode(array('method' => 'signup', 'message' => 'User registration failed,please try again.', 'success' => 0));
            }
        } else {
            return  json_encode(array('method' => 'signup', 'message' => 'Something went wrong,please try later.', 'success' => 0));
        }
    }


    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @return [string] acce
     * 'ss_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $input = $request->all();
        if (is_numeric($input['emailorphone'])) {

            $requestData = array('emailorphone' => 'required|string', 'password' => 'required', 'device_type' => 'required|integer', 'device_id' => 'required|string', 'device_token' => 'required|string', 'latitude' => 'required', 'longitude' => 'required');
            $credentials = array('phone_number' => $input['emailorphone'], 'password' => $input['password']);
        } else {

            $requestData = array('emailorphone' => 'required|string|email', 'password' => 'required', 'device_type' => 'required|integer', 'device_id' => 'required', 'device_token' => 'required|string', 'latitude' => 'required', 'longitude' => 'required');
            $credentials = array('email' => $input['emailorphone'], 'password' => $input['password']);
        }

        $validator = Validator::make($input, $requestData);
        if ($validator->fails()) {
            $ret = array('success' => 0, 'message' => $validator->messages()->first());
            return json_encode($ret);
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $languages = Languages::select('id', 'country', 'country_code', 'flag')->get();
            $updateLatLong = User::where('id', $user->id)->update(['latitude' => $input['latitude'], 'longitude' => $input['longitude']]);
            if ($updateLatLong) {
                $userData = User::where('id', $user->id)->first()->toArray();
                $userD = userDetails::where('user_id', $user->id)->first();
                if ($userD) {
                    $user_D = $userD->toArray();
                    $commonData = array_merge($userData, $user_D);
                    unset($commonData['id']);
                    $commonData['id'] = $commonData['user_id'];
                } else {
                    $commonData = $userData;
                }
            }

            if ($commonData['real_name'] == '' || $commonData['app_seen_name'] == '' || $commonData['about_me'] == '' || ($commonData['whatsapp'] == '' && $commonData['instagram'] == '' && $commonData['snapchat'] == '' && $commonData['twitter'] == '' && $commonData['skype'] == '' && $commonData['linkedin'] == '')) {
                $isProfileUpdated = 0;
                $commonData['isProfileUpdated'] = $isProfileUpdated;
            } else {
                $isProfileUpdated = 1;
                $commonData['isProfileUpdated'] = $isProfileUpdated;
            }

            if ($user) {
                $device_data = DeviceData::where(['user_id' => $user->id, 'device_id' => $input['device_id'], 'device_type' => $input['device_type'], 'device_token' => $input['device_token']])->first();

                if ($device_data) {
                } else {
                    $update_device_data =   DeviceData::insert(['user_id' => $user->id, 'device_id' => $input['device_id'], 'device_type' => $input['device_type'], 'device_token' => $input['device_token']]);
                }
            }
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            /* return response()->json([
                'data' => array('userData' => $commonData, 'access_token' => array(
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer')),
                    'success' => 1
                    ]); */

            return response()->json([
                'data' => array('userData' => $commonData, 'languages' => $languages),
                'success' => 1
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'message' => trans('message.invalid_credential')
            ]);
        }
    }

    public function update_availability(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'user_id'                   => 'required',
            'is_available'              => 'required',
        ]);

        if ($validator->fails()) {
            $ret = array('success' => 0, 'message' => $validator->messages()->first());
            return json_encode($ret);
        }

        $update_device_data = User::where(["id" => $input['user_id']])->update(['is_available' => $input['is_available']]);

        $updateLatLong = User::where('id', $input['user_id'])->update(['latitude' => $input['latitude'], 'longitude' => $input['longitude']]);
        if ($updateLatLong) {
            $userData = User::where('id', $input['user_id'])->first();
        }

        if ($update_device_data) {
            $ret = array('success' => 1, 'message' => trans('message.update_availability'));
        } else {
            $ret = array('success' => 0, 'message' => trans('message.update_availability_failed'));
        }

        return json_encode($ret);
    }

    public function updateLanguage(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',
            'language'  => 'required',
        ]);

        if ($validator->fails()) {
            $ret = array('success' => 0, 'message' => $validator->messages()->first());
            return json_encode($ret);
        }

        $update_data = User::where(["id" => $input['user_id']])->update(['language' => $input['language']]);

        if ($update_data) {
            $ret = array('success' => 1, 'message' => trans('message.lang_update'));
        } else {
            $ret = array('success' => 0, 'message' => trans('message.lang_update_failed'));
        }

        return json_encode($ret);
    }


    public function updateLatlong(Request $request)
    {
        $input = $request->all();
        $update_LatLong = 1;
        $validator = Validator::make($request->all(), [
            'latitude'  => 'required',
            'longitude' => 'required',
            'user_id'   => 'required'
        ]);

        if ($validator->fails()) {
            $ret = array('success' => 0, 'message' => $validator->messages()->first());
            return json_encode($ret);
        }

        //$updateLatLong = User::where('id', $input['user_id'])->update(['latitude' => $input['latitude'], 'longitude' => $input['longitude']]);

        $updateLatLong  = User::updateOrCreate(
            ['id' => $input['user_id']],
            ['latitude' => $input['latitude'], 'longitude' => $input['longitude']]
        );

        /* $results = Devicedata::where(['user_id' => $input['user_id']])->get();
        $results =  $results->toArray();
        for ($i = 0; $i < count($results); $i++) {
            $this->PushNotificationRepository->sendPushNotification($results[$i]['device_token'], "Friend Request", 'Testing sent you friend request.');
        } */

        if($updateLatLong){
            $ret = array('success' => 1, 'message' => trans('message.latlong_update'));
        } else {
            $ret = array('success' => 0, 'message' => trans('message.error'));
        }

        return json_encode($ret);


    }

    public function SearchUsers(Request $request)
    {
        $ids = array();
        $input = $request->all();
        $update_LatLong = 1;
        $validator = Validator::make($request->all(), [
            'latitude'  => 'required',
            'longitude' => 'required',
            'user_id'   => 'required'
        ]);

        if ($validator->fails()) {
            $ret = array('success' => 0, 'message' => $validator->messages()->first());
            return json_encode($ret);
        }
        //$updateLatLong = User::where('id', $input['user_id'])->update(['latitude' => $input['latitude'], 'longitude' => $input['longitude']]);
        $deviceTokens = Devicedata::where('device_token',$input['device_token'])->get();
        if ($deviceTokens == '') {
          $updateLatLong  = User::updateOrCreate(
              ['id' => $input['user_id']],
              ['device_token' => $input['device_token'], 'device_id' => $input['device_id']]
          );
        }

        $updateLatLong  = User::updateOrCreate(
            ['id' => $input['user_id']],
            ['latitude' => $input['latitude'], 'longitude' => $input['longitude']]
        );

        if ($updateLatLong) {
            $query = $this->getByDistance($input['latitude'], $input['longitude'], $input['distance'], $input['user_id']);
            //Extract the id's
            foreach ($query as $q) {
                array_push($ids, $q->id);
            }
            // Get the listings returned ids
            if ($ids) {
                $arrD = array();
                $resultsData = User::with('userDetails')->whereIn('id', $ids)->orderBy('id', 'DESC')->get()->toArray();
                foreach ($resultsData as $key => $value) {
                    $getRequest = DB::table('friend_list')
                        ->select('friend_list.*')
                        ->where('sender_id', $input['user_id'])
                        ->where('reciver_id', $value['user_details']['user_id'])
                        ->where('request_status', 0)
                        ->get();

                    if ($getRequest->count() > 0) {
                        $resultsData[$key]['alreadySent'] = 1;
                    } else {
                        $resultsData[$key]['alreadySent'] = 0;
                    }
                }
            } else {
                $resultsData = '';
            }

            if ($resultsData) {
                $ret = array('success' => 1, 'message' => 'users fetched successfully.', 'nearbyusers' => $resultsData);
            } else {
                $ret = array('success' => 0, 'message' => trans('message.no_data_found'));
            }
        } else {
            $ret = array('success' => 0, 'message' => trans('message.error'));
        }

        return json_encode($ret);
    }

    public function getByDistance($lat, $lng, $distance, $user_id = null)
    {

        $res = $this->getFriends($user_id);
        $usersStr = implode(',', $res);
        $distance = 0.5;
        if ($res) {
            $results = DB::select(DB::raw('SELECT id, ( 6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat . ') ) * sin( radians(latitude) ) ) ) AS distance FROM users WHERE is_available = 1 and id NOT IN (' . $usersStr . ') and id NOT IN (' . $user_id . ') HAVING distance < ' . $distance . ' ORDER BY distance'));
        } else {
            $results = DB::select(DB::raw('SELECT id, ( 6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat . ') ) * sin( radians(latitude) ) ) ) AS distance FROM users WHERE is_available = 1 and id NOT IN (' . $user_id . ') HAVING distance < ' . $distance . ' ORDER BY distance'));
        }
        return $results;


        // get distance in kilometer
        // $res = $this->getFriends($user_id);
        // $usersStr = implode(',', $res);

        // if ($res) {
        //     $results = DB::select(DB::raw('SELECT id, ( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat . ') ) * sin( radians(latitude) ) ) ) AS distance FROM users WHERE is_available = 1 and id NOT IN (' . $usersStr . ') and id NOT IN (' . $user_id . ') HAVING distance < ' . $distance . ' ORDER BY distance'));
        // } else {
        //     $results = DB::select(DB::raw('SELECT id, ( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat . ') ) * sin( radians(latitude) ) ) ) AS distance FROM users WHERE is_available = 1 and id NOT IN (' . $user_id . ') HAVING distance < ' . $distance . ' ORDER BY distance'));
        // }
        // return $results;
    }

    public function getFriends($user_id = null)
    {
        $ids = array();
        $getFriends = friendList::withTrashed()->select('reciver_id')->where(['sender_id' => $user_id])->whereIn('request_status', [1, 2])->get();

        foreach ($getFriends as $val) {
            array_push($ids, $val->reciver_id);
        }

        return $ids;
    }

    public function EditProfilepic(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        if ($validator->fails()) {
            $ret = array('success' => 0, 'message' => $validator->messages()->first());
            return json_encode($ret);
        }

        $user = User::where('id', $input['user_id'])->first();
        if ($user) {
            $fileName = time() . '.' . $request->profile_pic->extension();
            $fileName = "profile_pic_" . $fileName;
            $request->profile_pic->move(public_path('uploads'), $fileName);

            $update_profile_data = userDetails::updateOrCreate(
                ['user_id' => $input['user_id']],
                ['profile_image' => $fileName]
            );

            if ($update_profile_data) {
                $ret = array('success' => 1, 'message' => trans('message.image_upload'), 'image_name' => $update_profile_data->profile_image);
            } else {
                $ret = array('success' => 0, 'message' => trans('message.image_upload_failed'));
            }
        } else {
            $ret = array('success' => 0, 'message' => trans('message.no_data_found'));
        }

        return json_encode($ret);
    }

    public function updateProfiledata(Request $request)
    {
        $input = $request->all();
        //echo "<pre>";print_r($input);die("--");
        $validator = Validator::make($request->all(), [
            /* 'firstname' => 'required',
            'lastname' => 'required' */
            'app_seen_name' => 'required',
            'real_name' => 'required',
            'about_me' => 'required'
        ]);

        if ($validator->fails()) {
            $ret = array('success' => 0, 'message' => $validator->messages()->first());
            return json_encode($ret);
        }

        $data = array(
            /* "firstname"      => $input['firstname'],
                        "lastname"          => $input['lastname'], */
            "app_seen_name"     => $input['app_seen_name'],
            "real_name"         => $input['real_name'],
            "profile_image"     => $input['profile_pic'],
            "whatsapp"          => $input['whatsapp'],
            "facebook"          => $input['facebook'],
            "instagram"         => $input['instagram'],
            "linkedin"          => $input['linkedin'],
            "skype"             => $input['skype'],
            "gmail"             => $input['gmail'],
            "snapchat"          => $input['snapchat'],
            "twitter"           => $input['twitter'],
            "profession"        => $input['profession'],
            "about_me"          => $input['about_me'],
        );

        $update_profile_data = userDetails::updateOrCreate(
            ['user_id' => $input['user_id']],
            $data
        );

        if ($update_profile_data['real_name'] == '' || $update_profile_data['app_seen_name'] == '' || $update_profile_data['about_me'] == '' || ($update_profile_data['whatsapp'] == '' && $update_profile_data['instagram'] == '' && $update_profile_data['snapchat'] == '' && $update_profile_data['twitter'] == '' && $update_profile_data['skype'] == '' && $update_profile_data['linkedin'] == '')) {
            $isProfileUpdated = 0;
            $update_profile_data['isProfileUpdated'] = $isProfileUpdated;
        } else {
            $isProfileUpdated = 1;
            $update_profile_data['isProfileUpdated'] = $isProfileUpdated;
        }

        if ($update_profile_data) {
            $ret = array('success' => 1, 'message' => trans('message.profile_update'), 'profile_data' => $update_profile_data);
        } else {
            $ret = array('success' => 0, 'message' => trans('message.error'));
        }

        return json_encode($ret);
    }

    public function getProfiledata(Request $request)
    {
        $input = $request->all();
        $userData = User::where('id', $input['user_id'])->first();
        $get_profile_data = userDetails::where('user_id', $input['user_id'])->first();
        $get_profile_data->email = $userData->email;
        $get_profile_data->language = $userData->language;
        $get_profile_data->credits = $userData->credits;
        //$get_profile_data->profile_image = url('/uploads').'/'.$get_profile_data->profile_image;

        if ($get_profile_data['real_name'] == '' || $get_profile_data['app_seen_name'] == '' || $get_profile_data['about_me'] == '' || ($get_profile_data['whatsapp'] == '' && $get_profile_data['instagram'] == '' && $get_profile_data['snapchat'] == '' && $get_profile_data['twitter'] == '' && $get_profile_data['skype'] == '' && $get_profile_data['linkedin'] == '')) {
            $isProfileUpdated = 0;
            $get_profile_data['isProfileUpdated'] = $isProfileUpdated;
        } else {
            $isProfileUpdated = 1;
            $get_profile_data['isProfileUpdated'] = $isProfileUpdated;
        }

        if ($get_profile_data->count() > 0) {
            $ret = array('success' => 1, 'message' => trans('message.profile_fetch'), 'profile_data' => $get_profile_data);
        } else {
            $ret = array('success' => 0, 'message' => trans('message.no_data_found'));
        }
        return json_encode($ret);
    }



    public function addFriends(Request $request)
    {
        $input = $request->all();
        $param = "addfriend";
        $already_send = friendList::where(['sender_id' => $input['sender_id'], 'reciver_id' => $input['reciver_id']])->first();

        $results = Devicedata::where(['user_id' => $input['reciver_id']])->get();

        if ($already_send) {
            $ret = array('success' => 0, 'message' => trans('message.already_sent'));
        } else {
            $credits = $this->getCredits($input['sender_id']);
            if($credits <= 0) {
                $ret = array('success' => 0, 'message' => trans('message.insufficient_credits'));
            }   else {
                $add_user = new friendList([
                    'sender_id'      => $input['sender_id'],
                    'reciver_id'     => $input['reciver_id']
                ]);
                $add_user->save();
                $insertedId = $add_user->id;

                if ($insertedId) {
                    if ($results->count() > 0) {
                        $uDetails = userDetails::where(['user_id' => $input['user_id']])->first()->toArray();
                        $results =  $results->toArray();
                        for ($i = 0; $i < count($results); $i++) {
                            $this->sendPushNotification($results[$i]['device_token'], "Friend Request", $uDetails['real_name'] . ' sent you friend request.', $param);
                        }
                    }
                    $this->deductCredits($input['sender_id'], 1); // 1 credit deduct on sending friend request
                    $ret = array('success' => 1, 'message' =>  trans('message.rqst_send'));
                } else {
                    $ret = array('success' => 0, 'message' =>  trans('message.error'));
                }
            }
        }

        return json_encode($ret);
    }

    public function getRequest(Request $request)
    {
        $input = $request->all();
        //$getRequest = friendList::with('pendingrequest')->where(['sender_id' => $input['user_id'], 'request_status' => 0])->get()->toArray();

        $getRequest = DB::table('friend_list')
            ->select('friend_list.id as friend_list_id', 'friend_list.sender_id as sender_id', 'friend_list.reciver_id as reciver_id', 'friend_list.request_status as request_status', 'users_details.*')
            ->join('users_details', 'users_details.user_id', '=', 'friend_list.sender_id')
            ->where('friend_list.reciver_id', $input['user_id'])
            ->where('friend_list.request_status', 0)
            ->get();
        //->toSql();


        if ($getRequest->count() > 0) {
            $ret = array('success' => 1, 'message' =>  trans('message.rqst_fetch'), 'friend_list' => $getRequest);
        } else {
            $ret = array('success' => 0, 'message' => trans('message.no_data_found'));
        }

        return json_encode($ret);
    }

    public function accept_friend_request(Request $request)
    {
        $input = $request->all();

        if ($input['request_status'] == 1) {
            $msg = trans('message.rqst_acpt');
            $pushmsg = 'accepted your friend request.';
            $param = 'Accepted';
        } elseif ($input['request_status'] == 2) {
            $msg = trans('message.rqst_reject');
            $pushmsg = 'rejected your friend request.';
            $param = 'Rejected';
        }

        $response = friendList::where(['sender_id' => $input['reciver_id'], 'reciver_id' => $input['sender_id']])->update(array('request_status' => $input['request_status']));

        $data = friendList::where(['sender_id' => $input['sender_id'], 'reciver_id' => $input['reciver_id']])->get();

        if ($data->count() > 0) {
            $response = friendList::where(['sender_id' => $input['sender_id'], 'reciver_id' => $input['reciver_id']])->update(array('request_status' => $input['request_status']));
        } else {

            //if ($input['request_status'] == 1) {

            $add_user = new friendList([
                'sender_id'      => $input['sender_id'],
                'reciver_id'     => $input['reciver_id'],
                'request_status' => $input['request_status']
            ]);
            $add_user->save();
            $insertedId =   $add_user->id;

                $results    =   Devicedata::where(['user_id' => $input['reciver_id']])->get();
                $u_Details  =   userDetails::where(['user_id' => $input['sender_id']])->first()->toArray();
                $results    =   $results->toArray();

                for ($i = 0; $i < count($results); $i++) {
                    $this->sendPushNotification($results[$i]['device_token'], "Friend Request ".$param."", $u_Details['app_seen_name'] .' '.$pushmsg, $param);
                }
            //}
        }


        if ($response) {
            $ret = array('success' => 1, 'message' => $msg);
        } else {
            $ret = array('success' => 0, 'message' => trans('message.error'));
        }

        return json_encode($ret);
    }


    public function getFriendshistory(Request $request)
    {
        $input = $request->all();
        $arr = array();
        $getFriends = friendList::with('usersFriendlist')->where(['sender_id' => $input['user_id']])->where('request_status', '!=',  0)->get();

        if ($getFriends->count() > 0) {
            $getFriends = $getFriends->toArray();
            foreach ($getFriends as $key => $value) {
                $data = User::where(['id' => $value['reciver_id']])->first()->toArray();
                $value['users_friendlist']['is_available'] = $data['is_available'];
                $arr[] = $value;
            }

            $ret = array('success' => 1, 'data' => $arr);
        } else {
            $ret = array('success' => 0, 'message' => trans('message.no_data_found'));
        }

        return $ret;
    }

    public function deleteHistory(Request $request)
    {
        $input = $request->all();
        $data = [
            'updated_at' => Carbon::now(),
            'deleted_at' => Carbon::now(),
        ];
        $frnd_list = friendList::where(['reciver_id' => $input['delete_user'], 'sender_id' => $input['user_id']])->update($data);
        $frndlist = friendList::where(['reciver_id' => $input['user_id'], 'sender_id' => $input['delete_user']])->update($data);

        if ($frnd_list) {
            $ret = array('success' => 1, 'message' => "data deleted successfully.");
        } else {
            $ret = array('success' => 0, 'message' => trans('message.error'));
        }

        return $ret;
    }

    public function logout(Request $request)
    {
        $input = $request->all();
        $userData = User::where('id', $input['user_id'])->first();
        $device_data = DeviceData::where(['user_id' => $input['user_id'], 'device_id' => $input['device_id'], 'device_token' => $input['device_token']])->first();

        if ($device_data) {
            $deviceData = DeviceData::where(['user_id' => $input['user_id'], 'device_id' => $input['device_id'], 'device_token' => $input['device_token']])->delete();

            if ($device_data) {
                $ret = array('success' => 1, 'message' => "logout successfully.");
            } else {
                $ret = array('success' => 0, 'message' => trans('message.error'));
            }
        } else {
            $ret = array('success' => 1, 'message' => "logout successfully.");
            // $ret = array('success' => 0, 'message' => trans('message.error'));
        }
        return $ret;
    }

    public function addCredits(Request $request) {
        $data = [];
        $data = $request->all();
        if(!isset($data['device_type'])) {
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if(!isset($data['device_type']) || !in_array($data['device_type'], [1,2]) || !isset($data['user_id'])) {
            $result = array('success' => 0, 'message' => trans('message.error'));
        }   else {
            $res = $this->updateCredits($data['user_id'], 5);
            if($data['device_type'] == 2) {
                $sub = UserSubscriptions::where('user_id',$data['user_id'])->first();
                if($sub){
                    $sub->package_name = $data['packageName'];
                    $sub->product_id = $data['productId'];
                    $sub->purchase_token = $data['token'];
                    // $sub->google_response = json_encode($result['data']);
                    $sub->save();
                }else{
                    $dataSub = [
                        'user_id' => $data["user_id"],
                        'package_name' => $data["packageName"],
                        'product_id' => $data["productId"],
                        'purchase_token' => $data["token"],
                        // 'google_response' => json_encode($result['data'])
                    ];
                    UserSubscriptions::create($dataSub);
                }
            }

            if($res) {
                $result = ['success' => 1, 'message' => trans('message.credits_added_100')];
            }   else {
                $result = array('success' => 0, 'message' => trans('message.error'));
            }




            // if($data['device_type'] == 2) { // call google api
            //     // echo "google purchase calling"; exit;
            //     $result = $this->verifyGooglePurchase($data);
            // }   elseif($data['device_type'] == 1) { // call apple api
            //     $result = $this->verifyApplePurchase($data);
            // }   else {
            //     $result = array('success' => 0, 'message' => trans('message.error'));
            // }
        }
        // if($result['success'] == 1) {
        //     $res = $this->updateCredits($data['user_id'], 5);
        //     if($data['device_type'] == 2) {
        //         $sub = UserSubscriptions::where('user_id',$data['user_id'])->first();
        //         if($sub){
        //             $sub->package_name = $data['packageName'];
        //             $sub->product_id = $data['productId'];
        //             $sub->purchase_token = $data['token'];
        //             $sub->google_response = json_encode($result['data']);
        //             $sub->save();
        //         }else{
        //             $dataSub = [
        //                 'user_id' => $data["user_id"],
        //                 'package_name' => $data["packageName"],
        //                 'product_id' => $data["productId"],
        //                 'purchase_token' => $data["token"],
        //                 'google_response' => json_encode($result['data'])
        //             ];
        //             UserSubscriptions::create($dataSub);
        //         }
        //     }

        //     if($res) {
        //         $result = ['success' => 1, 'message' => trans('message.credits_added_100')];
        //     }   else {
        //         $result = array('success' => 0, 'message' => trans('message.error'));
        //     }
        // }   else {
        //     $result = array('success' => 0, 'message' => trans('product_verification_failed'));
        // }
        return json_encode($result, true);
    }

    public function verifyGooglePurchase($data = []) {
        if(!empty($data)) {
            if(!isset($data['packageName']) || !isset($data['productId']) || !isset($data['token']) || !isset($data['device_type']) || $data['packageName'] == "" || $data['productId'] == "" || $data['token'] == "" || !in_array($data['device_type'], [1,2])) {
                $ret = array('success' => 0, 'message' => trans('message.error'));
            }   else {
                $api_url = 'https://www.googleapis.com/androidpublisher/v3/applications/' . $data['packageName'] . '/purchases/products/' . $data['productId'] . '/tokens/' . $data['token'];

                $refreshToken = $this->refreshAccessToken();

                $access_token = $refreshToken['access_token'] ?? "";

                $authorization = "Authorization: Bearer ".$access_token;
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $api_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                        'content-type: application/json',
                        $authorization
                    ]
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    $ret = array('success' => 0, 'message' => trans('message.error'));
                } else {
                    $result = json_decode($response);
                    if(isset($result->error) && isset($result->error->message)) {
                        $ret = array('success' => 0, 'message' => $result->error->message);
                    }   else {
                        $ret = array('success' => 1, 'message' => 'success','data'=>$result);
                    }
                }
            }
        }   else {
            $ret = array('success' => 0, 'message' => trans('message.error'));
        }
        return $ret;
    }

    public function verifyApplePurchase($data = []) {
        if(isset($data['receipt-data'])) {
            $password_for_api = "397ba93185834cd987ca495b06c1ac6b";
            $params = [
                'receipt-data' => $data['receipt-data'],
                'password' => $password_for_api
            ];

            $reqBody = json_encode($params, true);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://sandbox.itunes.apple.com/verifyReceipt",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $reqBody,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                $res = ['success' => 0, 'message' => trans('message.error')];
                // echo "cURL Error #:" . $err;
            } else {
                $result = json_decode($response);
                if(isset($result->receipt) && isset($result->status)) {
                    if($result->status == 0) {
                        $res = ['success' => 1, 'message' => 'valid receipt','data'=>$result];
                    }   else {
                        $res = ['success' => 0, 'message' => 'Invalid Receipt'];
                    }
                }   else {
                    $res = ['success' => 0, 'message' => 'Invalid Receipt'];
                }
            }
        }   else {
            $res = ['success' => 0, 'message' => 'Invalid Receipt'];
        }
        return $res;
    }

    public function mytest() {
        $package_name = "com.roberto.hime"; // "com.softradix.hime";
        $product_id = "hime_credits"; // "test_credit_01";
        $myPurchaseToken = "mgdjnhffiljmlaogpbcbcomn.AO-J1OxINfU4teD4HbhLiiXshb-XveMiOjCIh0DpmAY5gP9AN59Hg1MSJXvpO4Pj0DcHjJs-uXE2QISdLmolGLu_-D3RniIT2dk-kPeR45oOi_3Kdgg1_Zk";
        // $package_name = "com.softradix.hime"; // "com.softradix.hime";
        // $product_id = "com.softradix.product.test"; // "test_credit_01";
        // $myPurchaseToken = "jjbojlafinphfdigflfplcnk.AO-J1Ox5FhHSLRRkX3-0OqsYDl9TVy7QqUM8gAz4YiK9GQX6NxWJPrdJ1dqD2rmqqyTtsuuceE_1vSDwflCj__knit3a5Mb8TRw3eASkJrgi7s9dctUH478SuWZSow49xp2s81wSzJsl";

        $refreshToken = $this->refreshAccessToken();

        $access_token = $refreshToken['access_token'] ?? "";

        $api_url = 'https://www.googleapis.com/androidpublisher/v3/applications/'.$package_name.'/purchases/products/'.$product_id.'/tokens/'.$myPurchaseToken;
        $authorization = "Authorization: Bearer ".$access_token;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                'content-type: application/json',
                $authorization
            ]
        ));

        $response=curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        echo "<pre>";
        print_r($response);
        exit;
    }



    public function refreshAccessToken()
    {
        $setting = Setting::first();
        if($setting) {
            $currentTime = strtotime(date('Y-m-d H:i:s'));
            $expiry_time = $currentTime + $response['expires_in'] - 10;
        }
        // Live Client Detail
        $clientID = env('CLIENT_ID');
        $clientSecret = env('CLIENT_SECRET');

        //$refreshToken = "1//014fdHr9PoNX0CgYIARAAGAESNwF-L9Irn1LMEvhtiHTRV4mY3hEGHQxIIkfv9cZ3OWBQgfwmohqK2DyJ8DAsduMLA3KrZILe9Vo";
	 $refreshToken = "1//04eG_v73esVUtCgYIARAAGAQSNwF-L9IrFIVy_VDJ_fgcc5jnA9_JXSOG6jNPDige3tyBQk5DrTPFrvXv2xopfQR8L1Ta4_ErkF8";
        // echo $clientID . " --- " . $clientSecret; die;
        //$refreshToken = "1//014fdHr9PoNX0CgYIARAAGAESNwF-L9Irn1LMEvhtiHTRV4mY3hEGHQxIIkfv9cZ3OWBQgfwmohqK2DyJ8DAsduMLA3KrZILe9Vo";
        // End Live Client Detail
        // Staging Detail
        // $clientID = "905162213358-jggs0i3icgk4fklvt5pfinli9clmoqjr.apps.googleusercontent.com";
        // $clientSecret = "aGTsBoONe9s1oMi7ysbmhG2S";
        // $refreshToken = "1//0gJF727GLVL5ECgYIARAAGBASNwF-L9IrrNTl3kuBBWhfL3oi2Uz8bGuy9THWaU03aHCxi93gxW3R3YaYrtrfa_YjyGF15HIyCQs";
        // End Staging Detail
        $client = new \GuzzleHttp\Client();
        try {

            $response = $client->post("https://accounts.google.com/o/oauth2/token", [
                'form_params' => [
                    'client_id' => "96563267157-d4986re0adomqllcms8lnur4cgm7f8i7.apps.googleusercontent.com", // $clientID,
                    'client_secret' => "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", // $clientSecret,
                    'refresh_token' => "1//04ajFGFy42fzSCgYIARAAGAQSNgF-L9IrZrT9NVIluVdiINe__zan0bl5kDo9iZE11x1Kqy5Wy0LzwQslJVVvbDfOnZVH3cweUg", // $refreshToken
                    'grant_type' => 'refresh_token'
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]);
        } catch(\Exception $e) {
            dd($e->getMessage());
        }


        $response = json_decode($response->getBody(),true);

        $setting = Setting::first();
        $currentTime = strtotime(date('Y-m-d H:i:s'));
        $expiry_time = $currentTime + $response['expires_in'] - 10;

        if($setting){
            $setting->access_token = $response['access_token'];
            $setting->expiry_time = $expiry_time;
            $setting->save();
        }else{
            $data = [
                'access_token' =>  $response['access_token'],
                'refresh_token' =>  $refreshToken,
                'expiry_time' =>    $expiry_time
            ];
            Setting::Create($data);
        }
        // dd($response['access_token']);
        return $response;
    }




    public function sendPushnotification($device_token, $title, $message, $status)
    {
        $push = new PushNotification('fcm');
        $push->setConfig([
            'priority' => 'normal',
            'time_to_live' => 3,
            'dry_run' => false
        ]);

        $extraNotificationData = [
            /* "message"    => $notificationData, */
            'title'      => $title,
            'body'       => $message,
            'status'     => $status,
            'sound'      => 'default',
            'badge'      => 1
            /*  'notificationType' => $type,
            'badge'      => $badge,
            'message'    => $message,
            'requestid'  => $requestid,
            'image'      => $notimage,
            'requestType' => $requestType */
        ];

        $push->setMessage([
            'notification' => [
                'title' => $title,
                'body'  => $message,
                'sound' => 'default'
            ],
            'data' =>  $extraNotificationData
        ])
            //->setApiKey('Server-API-Key')
            // OLD //->setApiKey('AAAA8xon_Uw:APA91bHIb3RbR168Eef47CUpYHf5Sg-Maz5SGTg75zjD7jfj2YRp1TSn4JvWxL6ABZLHkkP6uohnASk5EDj7PDkDgcRNrKgeoEv2EnudR7QnsnQwXIrMFFC8Rn3KWLDhTr0zRazSCoyb')
            ->setApiKey('AAAAdedah3s:APA91bF_UT5nYw7PqeqQ_Pzjv4wpY-s8EYAc_kmb_Y6m2LInC5teF5VDXaly2hnAbhZvItI8abohOEB33Uba61gHBkiNVzFkdw7h0Coy9wybUo2N-fNCLkFF-d-0CfXN5ov-ntcmLBTU')
            ->setDevicesToken($device_token)
            ->send();
    }

    public function getCredits($sender_id = 0) {
        return (int) User::where('id', $sender_id)->first()->credits;
    }

    public function deductCredits($sender_id = 0, $credits = 0) {
        $user_credits = (int) User::where('id', $sender_id)->first()->credits;
        if($user_credits > 0) {
            $final_credits = $user_credits - $credits;
            $usr = User::find($sender_id);
            $usr->credits = $final_credits;
            // $res = User::where('id', $sender_id)->update(['credits', $final_credits]);
            if($usr->save()) {
                return true;
            }
        }
        return false;
    }

    public function updateCredits($user_id = null, $credits = 0) {
        $user_credits = (int) User::where('id', $user_id)->first()->credits;

        $final_credits = $user_credits + $credits;
        $usr = User::find($user_id);
        $usr->credits = $final_credits;
        if($usr->save()) {
            return true;
        }

        return false;
    }
}
