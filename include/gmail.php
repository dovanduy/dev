<?php

class Gmail {

    protected static $client; // for application module
    protected static $google_app_id = '1035781535742-6a2584sfhodr6hbgq6227aektflm27rf.apps.googleusercontent.com'; // for application module
    protected static $google_app_secret = '60KJBrtGo7dlJcBsypcYJPGS'; // for application module
    protected static $google_app_redirect_uri = 'http://vuongquocbalo.dev/glogin'; // for application module
    protected static $google_access_token = '{"access_token":"ya29.Ci8MA_5Tff2UJ_4RjH4jXXd8-1qVnPlvr2gO7W-HQP-J3nGUu7H3zuX9o8enOJGFSA","token_type":"Bearer","expires_in":3600,"id_token":"eyJhbGciOiJSUzI1NiIsImtpZCI6IjBjMzU0YjIzYmM3NmFmZjYxODVkYjNjZjQyODA3ZTFmZWNiOWU3YzAifQ.eyJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiYXRfaGFzaCI6Iko3NDhjUVZwZDh4My1ZdFlLOWFqTHciLCJhdWQiOiIxMDM1NzgxNTM1NzQyLTZhMjU4NHNmaG9kcjZoYmdxNjIyN2Fla3RmbG0yN3JmLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwic3ViIjoiMTA5NDU3MDg3OTU0MDM3NzQwODgxIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsImF6cCI6IjEwMzU3ODE1MzU3NDItNmEyNTg0c2Zob2RyNmhiZ3E2MjI3YWVrdGZsbTI3cmYuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJlbWFpbCI6InRoYWlsdm5AZ21haWwuY29tIiwiaWF0IjoxNDY2ODQ2NzQ2LCJleHAiOjE0NjY4NTAzNDZ9.aMZOQU2kzb1Mg9oqeWy0l39cRMNwxyqXZOuO4UDs8nQzLY1hM82Igo2Rhqv8zh3Xav0rI5uPv9QUrYu7HUO3dbDOL4OxRIOGSMHhEin9R1rR0LK-DgosXZk10wa2lA5JuPLPzPy5E0BwYvVEA6TMMu8UMI2HCTOl9MY68LQXo2Y70N9PeOZco2OEBQKI0dzk4cKxxhszhF0_-wjIdsEwMUW3yL7XUAeJ8iS1-t2KZHRdwDT5LrD-Q-ZKnDq4cPMu61-gKme6IEKvo7AkXQhanx7twmCjxcf7EFTKW_HuoqMqZMQTOmFVXy2KEW'; // for application module

	public static function config() {
		
	}
	
    public static function instance() {
        static::$client or static::_init();
        return static::$client;
    }

    public static function _init() {        
        static::$client = new \Google_Client();
        static::$client->setClientId(static::$google_app_id);
        static::$client->setClientSecret(static::$google_app_secret);
        static::$client->setRedirectUri(static::$google_app_redirect_uri);
        static::$client->setAccessToken(static::$google_access_token);
    }

	public static function encodeRecipients($recipient) {
		$recipientsCharset = 'utf-8';
		if (preg_match("/(.*)<(.*)>/", $recipient, $regs)) {
			$recipient = '=?' . $recipientsCharset . '?B?'.base64_encode($regs[1]).'?= <'.$regs[2].'>';
		}
		return $recipient;
	}
	
	// http://stackoverflow.com/questions/26627755/send-mail-with-attachment-using-gmail-new-api-but-it-is-not-displayed-for-recei
	public static function getRowMessage($data) {
		$rowMessage = "";
		$boundary = uniqid(rand(), true);
		$charset = 'utf-8';
		
		$rowMessage .= 'To: ' . static::encodeRecipients($data['to_name'] . " <" . $data['to_email'] . ">") . "\r\n";
		$rowMessage .= 'From: '. static::encodeRecipients($data['from_name'] . " <" . $data['from_email'] . ">") . "\r\n";
		
		$rowMessage .= 'Subject: =?' . $data['subject'] . '?B?' . base64_encode($data['subject']) . "?=\r\n";
		$rowMessage .= 'MIME-Version: 1.0' . "\r\n";
		$rowMessage .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\r\n";
		
		/*
		$filePath = '/home/server/Downloads/credentials.csv';
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
		$mimeType = finfo_file($finfo, $filePath);
		$fileName = 'credentials.csv';
		$fileData = base64_encode(file_get_contents($filePath));
		
		$rowMessage .= "\r\n--{$boundary}\r\n";
		$rowMessage .= 'Content-Type: '. $mimeType .'; name="'. $fileName .'";' . "\r\n";            
		$rowMessage .= 'Content-ID: <' . $strSesFromEmail . '>' . "\r\n";            
		$rowMessage .= 'Content-Description: ' . $fileName . ';' . "\r\n";
		$rowMessage .= 'Content-Disposition: attachment; filename="' . $fileName . '"; size=' . filesize($filePath). ';' . "\r\n";
		$rowMessage .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
		$rowMessage .= chunk_split(base64_encode(file_get_contents($filePath)), 76, "\n") . "\r\n";
		$rowMessage .= '--' . $boundary . "\r\n";
		*/
		
		$rowMessage .= "\r\n--{$boundary}\r\n";
		$rowMessage .= 'Content-Type: text/plain; charset=' . $charset . "\r\n";
		$rowMessage .= 'Content-Transfer-Encoding: 7bit' . "\r\n\r\n";
		$rowMessage .= strip_tags($data['content']) . "\r\n"; // plan text

		$rowMessage .= "--{$boundary}\r\n";
		$rowMessage .= 'Content-Type: text/html; charset=' . $charset . "\r\n";
		$rowMessage .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
		$rowMessage .= $data['content'] . "\r\n";
		
	}
	
    public static function send($data, $accessToken, &$errorMessage = '') {     
        try {           
			$rowMessage = "";
			$boundary = uniqid(rand(), true);
			$charset = 'utf-8';
			
			$rowMessage .= 'To: ' . static::encodeRecipients($data['to_name'] . " <" . $data['to_email'] . ">") . "\r\n";
			$rowMessage .= 'From: '. static::encodeRecipients($data['from_name'] . " <" . $data['from_email'] . ">") . "\r\n";
			
			$rowMessage .= 'Subject: =?' . $data['subject'] . '?B?' . base64_encode($data['subject']) . "?=\r\n";
			$rowMessage .= 'MIME-Version: 1.0' . "\r\n";
			$rowMessage .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\r\n";
			
			/*
			$filePath = '/home/server/Downloads/credentials.csv';
			$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
			$mimeType = finfo_file($finfo, $filePath);
			$fileName = 'credentials.csv';
			$fileData = base64_encode(file_get_contents($filePath));
			
			$rowMessage .= "\r\n--{$boundary}\r\n";
			$rowMessage .= 'Content-Type: '. $mimeType .'; name="'. $fileName .'";' . "\r\n";            
			$rowMessage .= 'Content-ID: <' . $strSesFromEmail . '>' . "\r\n";            
			$rowMessage .= 'Content-Description: ' . $fileName . ';' . "\r\n";
			$rowMessage .= 'Content-Disposition: attachment; filename="' . $fileName . '"; size=' . filesize($filePath). ';' . "\r\n";
			$rowMessage .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
			$rowMessage .= chunk_split(base64_encode(file_get_contents($filePath)), 76, "\n") . "\r\n";
			$rowMessage .= '--' . $boundary . "\r\n";
			*/
			
			$rowMessage .= "\r\n--{$boundary}\r\n";
			$rowMessage .= 'Content-Type: text/plain; charset=' . $charset . "\r\n";
			$rowMessage .= 'Content-Transfer-Encoding: 7bit' . "\r\n\r\n";
			$rowMessage .= strip_tags($data['content']) . "\r\n"; // plan text

			$rowMessage .= "--{$boundary}\r\n";
			$rowMessage .= 'Content-Type: text/html; charset=' . $charset . "\r\n";
			$rowMessage .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
			$rowMessage .= $data['content'] . "\r\n";
		
            static::$client->setAccessToken($accessToken);
			$mime = rtrim(strtr(base64_encode($rowMessage), '+/', '-_'), '=');                
			$msg = new Google_Service_Gmail_Message();
			$msg->setRaw($mime);
			$service->users_messages->send("me", $msg);
			
            $response = static::instance()->post("/me/feed", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function postToGroup($groupId, $data, $accessToken, &$errorMessage = '') {
       try {
            $response = static::instance()->post("/{$groupId}/feed", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function commentToPost($postId, $data, $accessToken, &$errorMessage = '') {
        try {
            if (empty($data['message'])) {
                $data['message'] = 'vuongquocbalo.com';
            }
            $response = static::instance()->post("/{$postId}/comments", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function meCreateAlbum($data, $accessToken, &$errorMessage = '') {
        try {
            $response = static::instance()->post("/me/albums", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function groupCreateAlbum($groupId, $data, $accessToken, &$errorMessage = '') {
        try {
            $response = static::instance()->post("/{$groupId}/albums", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function addPhotoToAlbum($albumId, $data, $accessToken, &$errorMessage = '') {
        try {
            $response = static::instance()->post("/{$albumId}/photos", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function uploadUpublishedPhoto($data, $accessToken, &$errorMessage = '') {
        try {
            $data['published'] = false;
            $response = static::instance()->post("/me/photos", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }
}
