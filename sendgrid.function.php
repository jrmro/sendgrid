<?php

/**
* Lightweight function that abstracts sending an html email using SendGrid's v3 Mail Send REST API via cURL:
* https://docs.sendgrid.com/for-developers/sending-email/curl-examples
*
* Email addresses can be expressed as `email@example.com` or `Name <email@example.com>`
*
* Example Usage:
*
* $sendgrid = sendgrid([
* 	'api_key' => 'SENDGRID_API_KEY', 
* 	'to' => ['jane.doe@example.com', 'John Doe <john.doe@example.com>'],
* 	'cc' => ['George Washington <g.washington@example.com>', 'abraham.lincoln@example.com'], // Optional
* 	'bcc' => ['Alexander Hamilton <a.hamilton@example.com>'], // Optional
* 	'from' => 'sender@example.com',
* 	'reply_to' => 'sender@example.com', // Optional. Will default to 'from'
* 	'subject' => 'My First Email',
* 	'content' => 'Hello <strong>World</strong>!',
* 	'attachments' => array( // Optional	
* 		[ 'content' => 'BASE64_ENCODED_CONTENT', 'type' => 'text/plain', 'filename' => 'attachment1.txt' ], 
* 		[ 'content' => 'BASE64_ENCODED_CONTENT', 'type' => 'text/plain', 'filename' => 'attachment2.txt' ], 
* 		[ 'content' => 'BASE64_ENCODED_CONTENT', 'type' => 'text/plain', 'filename' => 'attachment3.txt' ], 
* 	)
* ]);
*
* @author     Joseph Romero
* @version    1.0
* ...
*/

if ( ! function_exists('sendgrid'))
{
	
	function sendgrid($params = array(

		'api_key' => null, 
		'to' => [], 
		'cc' => [], // Optional
		'bcc' => [], // Optional
		'from' => null, 
		'reply_to' => null, // Optional. Will default to 'from'
		'subject' => null,
		'content' => null,
		'attachments' => array( // Optional 
			[ 'content' => null, 'type' => null, 'filename' => null ]
		 )
		 
	))
	{


		// Check for required params

			$missing_params = array();
			
			foreach(['api_key', 'to', 'from', 'subject', 'content'] as $required)
			{
				if(empty($params[$required])) $missing_params[] = $required;
			}
				
			if( ! empty($missing_params)) return array('error' => true, 'message' => 'The following required parameters are missing: ' . implode(', ', $missing_params));


		// Create $fields array template

			$fields = array(

					'personalizations' => array(
						[
							'to' => array(
								['email' => 'email@example.com'],
							),
							
							'cc' => array(
								['email' => 'email@example.com'],
							),

							'bcc' => array(
								['email' => 'email@example.com'],
							),
						]
					),
					'from' => ['email' => $params['from']], 
					'reply_to' => ['email' => ! empty($params['reply_to']) ? $params['reply_to'] : $params['from']], // Default to 'from'. SendGrid does not accept empty reply_to email.
					'subject' => $params['subject'],
					'content' => array(
						[
							'type' => 'text/html',
							'value' => $params['content'],
						],
					),
					'attachments' => array(
						[
							'content' => 'BASE64_ENCODED_CONTENT',
							'type' => 'text/plain',
							'filename' => 'attachment.txt'
						]
					)

			);

				
		// Set personalizations

			foreach(['to', 'cc', 'bcc'] as $personalization) 
			{

				$fields['personalizations'][0][$personalization] = array();  // Reset placeholder personalization in template

				if( isset($params[$personalization]) )
				{

					if( ! is_array($params[$personalization]) ) return array('error' => true, 'message' => "'{$personalization}' email address(es) must be specified in an array.");

					foreach($params[$personalization] as $email)
					{
						if( ! empty($email)) $fields['personalizations'][0][$personalization][] = ['email' => $email];	
					}
					
				}

				if(empty($fields['personalizations'][0][$personalization])) unset($fields['personalizations'][0][$personalization]); // Purge placeholder personalizations in template (rather than reset to empty arrays). SendGrid does not accept empty personalizations.
				
			}


		// Set attachments
		
			$fields['attachments'] = array(); // Reset placeholder attachments in template

			if( isset($params['attachments']) )
			{

				if( ! is_array($params['attachments']) ) return array('error' => true, 'message' => 'Attachments must be included in an array.');

				foreach($params['attachments'] as $attachment)
				{

					if( empty($attachment['content']) || empty($attachment['type']) || empty($attachment['filename']) ) return array('error' => true, 'message' => "Each attachment must be an array that includes values for 'content', 'type', and 'filename'.");

					$fields['attachments'][] = array(
						'content' => $attachment['content'],
						'type' => $attachment['type'],
						'filename' => $attachment['filename']
					);

				}

			} 

			if(empty($fields['attachments'])) unset($fields['attachments']); // Purge placeholder attachments (rather than submit empty array).


		// Send to SendGrid endpoint using cURL 

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.sendgrid.com/v3/mail/send',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => json_encode($fields),
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer ' . $params['api_key'],
					'Content-Type: application/json'
				),
				CURLOPT_SSL_VERIFYPEER => false,
			));

			$response = curl_exec($curl);

			curl_close($curl);


		// Output Response	
		
			// cURL Failed
			if($response === false) return array('error' => true, 'message' => 'cURL failed.');		
			
			// SendGrid Error
			$response = json_decode($response, true);
			if( ! empty($response['errors'][0]['message'])) return array('error' => true, 'message' => $response['errors'][0]['message']);
			
			// Success
			return array('error' => false, 'message' => 'Success!');

	}
}
