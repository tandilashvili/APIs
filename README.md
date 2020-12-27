API Details:

API Type: REST
API Method: GET
API Format: XML
API Parameters: personal_id ([0-9]{11})

API URLs:
200 OK: http://localhost/APIs/server/?personal_id=01015021210
404 Not Found: http://localhost/APIs/server/?personal_id=01015021213
400 Bad Request: http://localhost/APIs/server/?personal_id=0101502121k

API Description:
Response array is converted into XML using converter function. 
Returns person details

