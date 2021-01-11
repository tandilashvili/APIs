API Details:

API Type: REST
API Method: POST
API Format: JSON
API Request caller: cURL
API Parameters: request_time, password_hash

API URL:
http://localhost/APIs/client/


API Description:
The API returns a letter that is Password protected, signed with Private Key.
The server signs letter using RSA Private key and sends the letter with the signature to the client.
The client checks the signature using server's public key (derived from a certificate previously sent to the client)
