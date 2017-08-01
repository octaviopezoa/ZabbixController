<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Libs\HttpRequest;


class TestController extends Controller
{
        /**
     * Create a new Zabbix API instance.
     *
     * @return void
     */
	public function __construct()
	{
		$this->zabbix = app('zabbix');
	}

    /**
	 * Get all the Zabbix host groups
     *
	 * @return array
	 */

    // Get all users from Zabbix 
    public function index()
    {
    	
        return $this->zabbix->userGet(['output' => 'extend']);
    } 


    //Get users in order
    public function userGet()
    {
        
        $user = $this->zabbix->userGet(['output' => 'extend']);
        
        foreach ($user as $usr) {
            # code...

            echo $usr->userid."\n";
            echo $usr->alias."\n";
            echo $usr->name."\n";
            echo $usr->surname."\n";
        }
    } 

    //Allow update user's media.  IS necesary that exist one media
    public function userUpdateMedia(Request $request)
    {
        
        $alias = $request->alias;
        $phone = $request->phone;
        $email = $request->email;
        

        $user = $this->zabbix->userGet(['output' => 'extend']);
        $mediax = $this->zabbix->usermediaGet(array(), 'media');         

        //search id user from user
        foreach ($user as $usr) {

            if ($usr->alias === $alias) 
                {               
                //search media for mediaid, userid, mediatypeid and 
                $iduser = $usr->userid;
                echo "user:".$usr->alias;
                foreach ($mediax as $medya) {
                    
                    
                    if ($medya->userid === $iduser) {
                    

                        $users= array(
                            'userid' => $iduser
                            
                        );

                        //This structure, is compatible with Zabbix's Json
                        $medias= array(
                            'mediatypeid' => 1,                                
                            'sendto' => $email,
                            'active' => 0,
                            'severity' => 63,
                            'period' => "1-7,00:00-24:00",

                        );
                      
                        $medias2= array(
                            'mediatypeid' => 4,
                            'sendto' => $phone,
                            'active' => 0,
                            'severity' => 63,
                            'period' => "1-7,00:00-24:00"
                        );

                        $params = array("jsonrpc" => "2.0", "method" => "user.updatemedia");
                        $params["params"] = array("users" => [$users], "medias" => [$medias, $medias2]);
                        $params["auth"] = $this->zabbix->authToken;
                        $params["id"] = 1;
                        
                        
                        $this->zabbix->userUpdateMedia($params["params"], $this->zabbix->authToken);
                        
                    } //endif email
                                     
                } //end foreach $media            
            } //endif $user
        } //end Foreach User
        
    } //end Funcion Update User Media
}
