# Lists

We'll assume you've already got API [authentication](./authentication.md) covered and you have your API key to hand (this is used in the Authorization header in all the examples below).

### Get all lists in your account

The `/api/lists` endpoint lists all the lists in your account, just send it a `GET` request using your API token in the Authorization header.

```
$ curl http://shopper.test/api/lists \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

The response body contains all the details for every list you've ever created.

<!-- lineNumbers: false -->
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "name": "Example List 1",
      "created_at": "2020-06-01T12:00:00.000000Z",
      "updated_at": "2020-06-01T12:00:00.000000Z"
    },
    {
      "id": 2,
      "user_id": 1,
      "name": "Example List 2",
      "created_at": "2020-06-01T12:00:00.000000Z",
      "updated_at": "2020-06-01T12:00:00.000000Z"
    }
  ]
}
```

### Getting a specific list

...

### Creating a list

To create a list send a `POST` request to the `/api/lists` endpoint containing the name of the list in the request payload.

```
$ curl -X POST http://shopper.test/api/lists \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example List 3"}'
```

If your request succeeds, the new list will be returned in the response body.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 3,
    "user_id": 1,
    "name": "Example List 3",
    "created_at": "2020-06-01T12:00:00.000000Z",
    "updated_at": "2020-06-01T12:00:00.000000Z"
  }
}
```

<!-- theme: info -->
> Notice how this result differs slightly from the response body when getting lists. It no longer wraps the lists in an array, the data key is just an object of the list attributes.

### Updating a list

To update a list send a `PATCH` request to the `/api/lists/{id}` endpoint containing the new name of the list in the request payload.

```
$ curl -X PATCH http://shopper.test/api/lists/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example List 1 Updated"}'
```

If your request succeeds, the updated list will be returned in the response body.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "Example List 1 Updated",
    "created_at": "2020-06-01T12:00:00.000000Z",
    "updated_at": "2020-06-01T12:00:00.000000Z"
  }
}
```

### Deleting a list

Use the `DELETE` method to delete a list through the API. In this example we'll delete the list with ID `1`.

```
$ curl -X DELETE http://shopper.test/api/lists/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

Notice how we're calling the `DELETE` method on the `/api/lists/1` endpoint. The `1` in the URL determines which list to delete.

<!-- theme: warning -->
> Please note, when deleting a list, this will also detach all items from the list and delete any items that aren't used anywhere else in your account. Just another way we keep our data nice and clean.

### Getting all meals for a specific list

...


### Adding a meal to a list

...
