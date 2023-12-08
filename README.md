# sendgrid

Lightweight function that abstracts sending an html email using SendGrid's v3 Mail Send REST API via cURL:

[cURL Examples for Common Use Cases (docs.sendgrid.com)](https://docs.sendgrid.com/for-developers/sending-email/curl-examples)


## Sample Usage

Email addresses can be expressed as `john.doe@example.com` or `John Doe <john.doe@example.com>`.

```
$sendgrid = sendgrid([
    'api_key' => 'SENDGRID_API_KEY', 
    'to' => ['jane.doe@example.com', 'John Doe <john.doe@example.com>'],
    'cc' => ['George Washington <g.washington@example.com>', 'abraham.lincoln@example.com'], // Optional
    'bcc' => ['Alexander Hamilton <a.hamilton@example.com>'], // Optional
    'from' => 'sender@example.com',
    'reply_to' => 'sender@example.com', // Optional. Will default to 'from'
    'subject' => 'My First Email',
    'content' => 'Hello <strong>World</strong>!',
    'attachments' => array( // Optional	
        [ 'content' => 'BASE64_ENCODED_CONTENT', 'type' => 'text/plain', 'filename' => 'attachment1.txt' ], 
        [ 'content' => 'BASE64_ENCODED_CONTENT', 'type' => 'text/plain', 'filename' => 'attachment2.txt' ], 
        [ 'content' => 'BASE64_ENCODED_CONTENT', 'type' => 'text/plain', 'filename' => 'attachment3.txt' ], 
    )
]);
```
## Return Values
Function returns an associative array with the following values: 'error' (bool) , 'message' (string), 'status' (int). Upon success, 'error' will be false (bool). HTTP status code is assigned to 'status'. A 'status' of 0 indicates that we did not connect with the mail server (either we didn't attempt a connection or the connection attempt was not successful).

## Author
Joseph Romero
[https://github.com/jrmro](https://github.com/jrmro)

## License
This code is released under the MIT License.
