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

### Get a specific list

If you don't want to get all lists, you can get a specifc list if you know its ID. Send a `GET` request to the `/api/lists/{id}` endpoint including the list ID in the URL.

```
$ curl -X POST http://shopper.test/api/lists/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

If your request succeeds, the list will be returned in the response body.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "Example List 1",
    "created_at": "2020-06-01T12:00:00.000000Z",
    "updated_at": "2020-06-01T12:00:00.000000Z"
  }
}
```

<!-- theme: info -->
> Notice how this result differs slightly from the response body when getting lists. It no longer wraps the lists in an array, the data key is just an object of the list attributes.

#### Including list items and/or meal

When getting a specific list you also have the option of including related resources in the response body, your two options for a list are:

- `items` - the items that have been created or added to the list.
- `meals` - the meals that have been added to the list.

In order to include a relationship in the response you can use the `include` query parameter and give it one of the values above.

```
$ curl -X POST http://shopper.test/api/lists/1?include=items \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

You can also include both values seperating them by a comma.

```
$ curl -X POST http://shopper.test/api/lists/1?include=items,meals \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

The response body returned from these requests will simply add a new key to the data object that includes an array of objects for each item or meal related to the list.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "Example List 1",
    "created_at": "2020-06-01T12:00:00.000000Z",
    "updated_at": "2020-06-01T12:00:00.000000Z",
    "items": [
      {
          "id": 1,
          "user_id": 1,
          "name": "Example Item 1",
          "created_at": "2020-06-15T09:37:14.000000Z",
          "updated_at": "2020-06-15T09:40:13.000000Z"
      }
    ],
    "meals": [
      {
          "id": 1,
          "user_id": 1,
          "name": "Example Meal 1",
          "created_at": "2020-06-15T09:37:14.000000Z",
          "updated_at": "2020-06-15T09:40:13.000000Z"
      }
    ]
  }
}
```

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

#### Creating a list by copying another

As well as creating a brand new list, you have the ability to copy another list you've previously created in your account. When copying a list this will also copy the previous lists items and meals over to the new list.

To do so, include the ID of the list you'd like to copy in the request payload.

```
$ curl -X POST http://shopper.test/api/lists \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example List 4", "list_id": 1}'
```

This request will create a new list with the name provided and copy all items and meals that are in the list with ID `1`.

There's one option you can use when copying a list and this controls whether all items are copied, or only incomplete items. By default the request will copy them all, but if you only want the incomplete items copied, set the `only_incomplete` key to `true`.

```
$ curl -X POST http://shopper.test/api/lists \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example List 5", "list_id": 1, "only_incomplete": true}'
```

The response body for both of these requests is exactly the same as when you create a brand new list, it will return the newly create list only.

<!--- theme:warning -->
> When copying only incomplete items, if there are no items on the new list for a meal that was on the previous list, this meal will not be copied. If there are some, or all items on the new list for a meal, then the meal will be copied to the new list.

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
> Please note, when deleting a list, this will detach all items from the list and delete any items that aren't used anywhere else in your account. It will also detach the list from any meals that have previously been added to to. Just another way we keep our data nice and clean.

### Getting all meals for a specific list

If you would like to get a list of all the meals currently added to a list you can send a `GET` request to the `/api/list-meals/{id}` endpoint.

```
$ curl http://shopper.test/api/list-meals/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

The response body for this request will contain all the details for every meal you've added to the list with and ID of `1`.

<!-- lineNumbers: false -->
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "name": "Example Meal 1",
      "created_at": "2020-06-01T12:00:00.000000Z",
      "updated_at": "2020-06-01T12:00:00.000000Z"
    },
    {
      "id": 2,
      "user_id": 1,
      "name": "Example Meal 2",
      "created_at": "2020-06-01T12:00:00.000000Z",
      "updated_at": "2020-06-01T12:00:00.000000Z"
    }
  ]
}
```

### Adding a meal to a list

To add a meal to a list you can send a `POST` request to the same endpoint `/api/list-meals/{id}` and include the `meal_id` in the request payload.

```
$ curl -X POST http://shopper.test/api/list-meals/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"meal_id": 1}'
```

This will not only attach the meal to the list but also all of its items as well. If successful, you will receive a `204 No Content` response from this endpoint.
