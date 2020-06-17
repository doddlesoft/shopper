# Meals

We'll assume you've already got API [authentication](./authentication.md) covered and you have your API key to hand (this is used in the Authorization header in all the examples below).

### Get all meals in your account

The `/api/meals` endpoint meals all the meals in your account, just send it a `GET` request using your API token in the Authorization header.

```
$ curl http://shopper.test/api/meals \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

The response body contains all the details for every meal you've ever created.

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

### Get a specific meal

If you don't want to get all meals, you can get a specifc meal if you know its ID. Send a `GET` request to the `/api/meals/{id}` endpoint including the meal ID in the URL.

```
$ curl -X POST http://shopper.test/api/meals/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

If your request succeeds, the meal will be returned in the response body.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "Example Meal 1",
    "created_at": "2020-06-01T12:00:00.000000Z",
    "updated_at": "2020-06-01T12:00:00.000000Z"
  }
}
```

<!-- theme: info -->
> Notice how this result differs slightly from the response body when getting meals. It no longer wraps the meals in an array, the data key is just an object of the meal attributes.

#### Including meal items and/or list

When getting a specific meal you also have the option of including related resources in the response body, your two options for a meal are:

- `items` - the items that have been created or added to the meal.
- `lists` - the lists that the meal has been added to.

In order to include a relationship in the response you can use the `include` query parameter and give it one of the values above.

```
$ curl -X POST http://shopper.test/api/meals/1?include=items \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

You can also include both values seperating them by a comma.

```
$ curl -X POST http://shopper.test/api/meals/1?include=items,lists \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

The response body returned from these requests will simply add a new key to the data object that includes an array of objects for each item or list related to the meal.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "Example Meal 1",
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
    "lists": [
      {
          "id": 1,
          "user_id": 1,
          "name": "Example List 1",
          "created_at": "2020-06-15T09:37:14.000000Z",
          "updated_at": "2020-06-15T09:40:13.000000Z"
      }
    ]
  }
}
```

### Creating a meal

To create a meal send a `POST` request to the `/api/meals` endpoint containing the name of the meal in the request payload.

```
$ curl -X POST http://shopper.test/api/meals \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Meal 3"}'
```

If your request succeeds, the new meal will be returned in the response body.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 3,
    "user_id": 1,
    "name": "Example Meal 3",
    "created_at": "2020-06-01T12:00:00.000000Z",
    "updated_at": "2020-06-01T12:00:00.000000Z"
  }
}
```

<!-- theme: info -->
> Notice how this result differs slightly from the response body when getting meals. It no longer wraps the meals in an array, the data key is just an object of the meal attributes.

### Updating a meal

To update a meal send a `PATCH` request to the `/api/meals/{id}` endpoint containing the new name of the meal in the request payload.

```
$ curl -X PATCH http://shopper.test/api/meals/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Meal 1 Updated"}'
```

If your request succeeds, the updated meal will be returned in the response body.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "Example Meal 1 Updated",
    "created_at": "2020-06-01T12:00:00.000000Z",
    "updated_at": "2020-06-01T12:00:00.000000Z"
  }
}
```

### Deleting a meal

Use the `DELETE` method to delete a meal through the API. In this example we'll delete the meal with ID `1`.

```
$ curl -X DELETE http://shopper.test/api/meals/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

Notice how we're calling the `DELETE` method on the `/api/meals/1` endpoint. The `1` in the URL determines which meal to delete.

<!-- theme: warning -->
> Please note, when deleting a meal, this will detach all items from the meal as well as detach the meal from any lists it has previously been added to. Just another way we keep our data nice and clean.
