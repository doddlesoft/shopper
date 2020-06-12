# Items

We'll assume you've already got API [authentication](./authentication.md) covered and you have your API key to hand (this is used in the Authorization header in all the examples below).

### Get all items in your account

The `/api/items` endpoint lists all the items in your account, just send a `GET` request to this endpoint using your API token in the Authorization header.

```
$ curl http://shopper.test/api/items \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

The response body contains all the details for every item you've ever created.

<!-- lineNumbers: false -->
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "name": "Example Item One",
      "created_at": "2020-06-01T12:00:00.000000Z",
      "updated_at": "2020-06-01T12:00:00.000000Z"
    },
    {
      "id": 2,
      "user_id": 1,
      "name": "Example Item Two",
      "created_at": "2020-06-01T12:00:00.000000Z",
      "updated_at": "2020-06-01T12:00:00.000000Z"
    }
  ]
}
```

#### Get all items for a specific list

If you're only wanting to get the items for a specific list, you can use the `filter` query parameter and give it the value `list:{id}`.

```
$ curl http://shopper.test/api/items?filter=list:1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

The response body is identical to the above example when getting items, they're just of course filtered to only contain the items on the list of the ID provided.

#### Get all items for a specific meal

Just like with lists you can also filter items by meal, just use the same `filter` query parameter and give it the value `meal:{id}`.

```
$ curl http://shopper.test/api/items?filter=meal:1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

Again, the response body is identical to the above example when getting items, they're just of course filtered to only contain the items on the meal of the ID provided.

#### Sorting items

When getting your items using any of the methods above, you have the option of sorting these using the `sort` query parameter.

There are three options for sorting your items, these are:
- `created_at` - chronologically using the date/time the item was first created.
- `name` - alphabetically using the item name.
- `meal` - alphabetically using the meal name the item was created for.

By default the results will be sorted in ascending order, to switch this to descending order prepend the sort value with a hyphen, for example `-name`.

If the sort query paramemter isn't provided in the request, the items `id` will be used as a fallback to sort in ascending order.

When using sort, the response body is structured in exactly the same way as all other `GET` requests, however, when sorting by meal the `meal_name` is appended to each item.

<!-- lineNumbers: false -->
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "name": "Example Item One",
      "created_at": "2020-06-01T12:00:00.000000Z",
      "updated_at": "2020-06-01T12:00:00.000000Z",
      "meal_name": "Example Meal"
    },
    ...
  ]
}
```

Items which don't have a meal will always appear after those that do, regardless of whether the `meal` is sorted in ascending or descending order.

<!-- theme: info -->
> Please note, the `meal` sort option is superfluous when filtering your items by a meal.

#### Filtering and sorting at the same time

You also have the option of using the `filter` and `sort` query parameters together in the same request. For example, you can get all items for a list or meal and sort them accordingly, simply use both query parameters in your request.

```
$ curl http://shopper.test/api/items?filter=list:1&sort=name \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

The response body returned from this request would give you results filtered for the list with ID `1` and sorted by the item `name` in ascending order.

#### Paginating items

...

### Creating an item

To create an item send a `POST` request to the `/api/items` endpoint containing the name of the item in the request payload.

```
$ curl -X POST http://shopper.test/api/items \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Item Three"}'
```

We :heart: JSON, so your request payload should always be valid JSON. The actual payload, when formatted, looks like this.

<!-- lineNumbers: false -->
```json
{
  "name": "Example Item Three"
}
```

If your request succeeds, the new item will be returned in the response body.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 3,
    "user_id": 1,
    "name": "Example Item Three",
    "created_at": "2020-06-01T12:00:00.000000Z",
    "updated_at": "2020-06-01T12:00:00.000000Z"
  }
}
```

<!-- theme: info -->
> Notice how this result differs slightly from the response body when getting items. It no longer wraps the items in an array, the data key is just an object of the item attributes.

#### Creating an item for a specific list

You also have the option of creating an item for a specific list. To do so include the list ID in the request payload along with the name of the item.

```
$ curl -X POST http://shopper.test/api/items \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Item Four", "list_id": 1}'
```

#### Creating an item for a specific meal

You also have the option of creating an item for a specific meal. To do so include the meal ID in the request payload along with the name of the item.

```
$ curl -X POST http://shopper.test/api/items \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Item Five", "meal_id": 1}'
```

The response body when creating an item for a list or meal is exactly the same as above, you just get the new item.

#### Adding an existing item to a list or meal

When adding an item to a list or meal, if you happen to already know the ID of the item you want to add, you can include this in the request payload instead of the name.

```
$ curl -X POST http://shopper.test/api/items \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"item_id": 1, "list_id": 1}'
```

This means no new item is created and the existing item is retrieved and added to the list or meal.

#### Keeping our data cleansed

We like to keep our data clean and tidy, and one of the ways we do this is by ensuring there are no duplicate entries in our database.

Therefore, whenever a `POST` request is sent to the `/api/items` endpoint we first check to see if there is another item with the same name. If an item is found, we will simply use and return this item rather than create a new one.

If an `item_id` is provided in the payload, there's no need to perform this check and the existing item is always used and returned.

The only difference in responses for these scenarios is that if a new item is created a `201 Created` status is returned, otherwise, if the item already exists a `200 OK` status is returned.

<!-- theme: warning -->
> Please note, if a `name` and `item_id` are both present in the same request the `item_id` will take precedence, meaning the existing item with the ID provided will be added to the list or meal.

### Updating an item

To update an item send a `PATCH` request to the `/api/items/{id}` endpoint containing the new name of the item in the request payload.

```
$ curl -X PATCH http://shopper.test/api/items/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Item One Updated"}'
```

If your request succeeds, the updated item will be returned in the response body.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "Example Item One Updated",
    "created_at": "2020-06-01T12:00:00.000000Z",
    "updated_at": "2020-06-01T12:00:00.000000Z"
  }
}
```

When updating an item that has been used on a list or meal, this needs to be preserved. Therefore, instead of updating the item we [create](#creating-an-item) a new item to preserve this history. A `201 Created` status is returned in this scenario along with the new item in the response body.

If the item hasn't been used on a list or meal, the item is updated and a `200 OK` status is returned along with the updated item in the response body.

#### Updating an item for a specific list

You also have the option of updating an item for a specific list. To do so include the list ID in the request payload along with the new name of the item.

```
$ curl -X PATCH http://shopper.test/api/items/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Item One Updated", "list_id": 1}'
```

#### Updating an item for a specific meal

You also have the option of updating an item for a specific meal. To do so include the meal ID in the request payload along with the new name of the item.

```
$ curl -X PATCH http://shopper.test/api/items/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Item One Updated", "meal_id": 1}'
```

When updating an item for a specific list or meal, if the item is used elsewhere on another list or meal, this needs to be preserved. Therefore, instead of updating the item, we detach it from the list or meal and [create](#creating-an-item) a new item to preserve this history. A `201 Created` status is returned in this scenario along with the new item in the response body.

If the item hasn't been used elsewhere, the item is updated and a `200 OK` status is returned along with the updated item in the response body.

### Deleting an item

Use the `DELETE` method to delete an item through the API. In this example we'll delete the item with ID `1`.

```
$ curl -X PATCH http://shopper.test/api/items/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

Notice how we're calling the `DELETE` method on the `/api/items/1` endpoint. The `1` in the URL determines which item to delete.

<!-- theme: warning -->
> Please note, when deleting an item that is also used on a list or meal, it will be removed from them too and all history will be lost.

#### Deleting an item from a specific list

You also have the option of deleting an item from a specific list rather than from everywhere. To do so include the list ID in the request payload.

```
$ curl -X DELETE http://shopper.test/api/items/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"list_id": 1}'
```

#### Deleting an item from a specific meal

You also have the option of deleting an item from a specific meal rather than from everywhere. To do so include the meal ID in the request payload.

```
$ curl -X DELETE http://shopper.test/api/items/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"meal_id": 1}'
```

When deleting an item from a specific list or meal, if it is used elsewhere then it is preserved and remains an item on this list or meal. If the item isn't used elsewhere it is deleted from your account.

For all `DELETE` requests a `204 No Content` status is returned with an empty response body.
