# API Authentication

### Registering through the API

To register a new user on Shopper through the API send a `POST` request to the `/api/register` endpoint which should include the following required payload.

- `name` - The name of the user.
- `email` - The email address of the user.
- `password` - The password to be used to authenticate the user.
- `password_confirmation` - Confirmation of the password provided above, these must match.
- `device_name` - The name of the device being used to register.

A request might look something like this:

```
$ curl http://shopper.test/api/register \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example User", "email": "user@example.com", "password": "password", "password_confirmation": "password", "device_name": "Example Device"}'
```

If your request succeeds, a new token will be returned for you to use to access the new users account through the API.

<!-- lineNumbers: false -->
```json
{
  "token": "personal_access_token"
}
```

### Signing in through the API

To sign in to an existing users account in exchange for a new token to access their account through the API, send a `POST` request to the `/api/sign-in` endpoint.

This request must contain the email address and password of the account you'd like to access, along with the name of the device sending the request.

```
$ curl http://shopper.test/api/sign-in \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"email": "user@example.com", "password": "password", "device_name": "Example Device"}'
```

If your request succeeds, a new token will be returned for you to use to access the users account through the API.

<!-- lineNumbers: false -->
```json
{
  "token": "personal_access_token"
}
```
