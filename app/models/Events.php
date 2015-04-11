<?php

class Events extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */

	protected $guarded = array('id');  // Important

	protected $table = 'events';

	public function category()
	{
		return $this->belongsTo('EventCategory', 'event_category_id');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function city()
	{
		return $this->belongsTo('City');
	}

	public function defaultPhoto()
	{
		return $this->belongsTo('Photo', 'default_photo_id');
	}

	public function coverPhoto()
	{
		return $this->belongsTo('Photo', 'cover_photo_id');
	}


	public static function checkSlugName($input){
		return Events::where('slug',$input)->count();
	}

	public static function createEvent($input) {
		$started_at = preg_split("/([\/: ])/", $input['started_at']);
		$ended_at = preg_split("/([\/: ])/", $input['ended_at']);
		
		$input =  array(
			'event_category_id'=> $input['event_category_id'],
			'city_id'=> $input['city_id'],
			'email'=> $input['email'],
			'name'=> $input['name'],
			'stewardship' => $input['stewardship'],
			'description' => $input['description'],
			'location' => $input['location'],
			'website_url' => $input['website_url'],
			'social_media_urls' => $input['social_media_urls'],
			'started_at' => mktime((int) $started_at[3], (int) $started_at[4],0,(int) $started_at[0],(int) $started_at[1],(int) $started_at[2]),
			'ended_at' => mktime((int) $ended_at[3], (int) $ended_at[4],0,(int) $ended_at[0],(int) $ended_at[1],(int) $ended_at[2]),
		 );

		$rules =  array(
			'event_category_id'=> 'required',
			'city_id'=> 'required',
			'email'=> 'required|email',
			'name'=> 'required',
			'stewardship' => 'required|min:20',
			'description' => 'required|min:20',
			'location' => 'required',
			'website_url' => 'required|url',
			'social_media_urls' => 'required',
			'started_at' => 'required',
			'ended_at' => 'required',
		 );

		$validator = Validator::make($input, $rules);

  	  	if ($validator->fails()) {
  	 		return $validator->errors()->all();
	    } 
	    else {
	    		$event = new Events;
	    		$event->fill($input);
	    		$event->save();
	    		
	    		// update 
	    		$update = Events::find($event->id);
				$update->fill(array(
				    'slug' => Events::checkSlugName($input['name']) > 0 ? strtolower(str_replace(' ', '-', $input['name'])).$event->id : strtolower(str_replace(' ', '-', $input['name'])),
				));
				$update->save();
	    		return "ok";
	    	try {

	   
	    	} catch (Exception $e) {
	    		return "no";
	    	}
	    }
	}
}