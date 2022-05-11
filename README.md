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
