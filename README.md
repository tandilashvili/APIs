API Details:

API Type: REST
API Method: POST
API Format: JSON
API Request caller: cURL
API Parameters: personal_id ([0-9]{11})

API URL:
200 OK:  http://localhost/APIs/client/?personal_id=01015021210
400 Bad Request:  http://localhost/APIs/client/?personal_id=010150_____
404 Not Found:  http://localhost/APIs/client/?personal_id=01015021215


API Description:
Hybrid Cryptosystem is implemented in this API. Steps:
- Symmetric key is generated on the client side
- Generated symmetric key is encrypted using recipient's public key 
- Data (personal id) is signed with sender's private key
- Data (personal id) is encrypted using the symmetric key
- Data sent from client includes: 
    1. Symmetric key encrypted by recipient's public key
    2. Personal ID encrypted by symmetric key
    3. Digital signature made by sender's private key on personal ID
- Recipient decrypts the secret key using private key
- Decrypts personal ID using the symmetric secret key
- Checks signature (made by sender) using private key (if it does not match, generates 401 error)
- Signs person details using private key (if person details found based on the personal ID)
- Encrypts person details data using the secret key
- Returns encrypted person details with its signature back to the client
- Client receives the response, checks API status, signature and shows person details if everything is OK
