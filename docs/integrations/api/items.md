# Items

We'll assume you've already got API [authentication](./authentication.md) covered and you have your API key to hand (this is used in the Authorization header in all the examples below).

### Get all items in your account

The `/api/items` endpoint lists all the items in your account, just send it a `GET` request using your API token in the Authorization header.

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
      "name": "Example Item 1",
      "created_at": "2020-06-01T12:00:00.000000Z",
      "updated_at": "2020-06-01T12:00:00.000000Z"
    },
    {
      "id": 2,
      "user_id": 1,
      "name": "Example Item 2",
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
- `meal` - alphabetically using the meal name the item relates to.

```
$ curl http://shopper.test/api/items?sort=name \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

By default the results will be sorted in ascending order, to switch this to descending order prepend the sort value with a hyphen, for example `-name`.

If the sort query paramemter isn't provided in the request, the items `id` will be used as a fallback to sort the results in ascending order.

When using sort, the response body is structured in exactly the same way as all other `GET` requests, however, when sorting by meal the `meal_name` is appended to each item.

<!-- lineNumbers: false -->
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "name": "Example Item 1",
      "created_at": "2020-06-01T12:00:00.000000Z",
      "updated_at": "2020-06-01T12:00:00.000000Z",
      "meal_name": "Example Meal"
    },
    ...
  ]
}
```

Items which don't have a meal will always appear after those that do, with a `null` value for `meal_name`, regardless of whether the `meal` is sorted in ascending or descending order.

<!-- theme: warning -->
> Please note, the `meal` sort option is superfluous when filtering your items by a meal.

#### Filtering and sorting at the same time

You also have the option of using the `filter` and `sort` query parameters together in the same request. For example, you can get all items for a list or meal and sort them accordingly, simply use both query parameters in your request.

```
$ curl http://shopper.test/api/items?filter=list:1&sort=name \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

The response body returned from this request will give you results filtered for the list with ID `1` and sorted by the item `name` in ascending order.

#### Paginating items

When using any of the `GET` requests to `/api/items` outlined above, you have the option of paginating your results using the `page[size]` and `page[number]` query parameters.

- `page[size]` - the number of items you would like returned.
- `page[number]` - the page number you'd like to offset the results by.

```
$ curl http://shopper.test/api/items?page[size]=2&page[number]=3 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

When paginating your results the response body contains the same `data` key which includes your items just like all other `GET` requests. But along with the results you'll also get some pagination-related metadata as well as useful links.

This is how the response body would look if you performed the above request and had a total of 10 items in your account.

<!-- lineNumbers: false -->
```json
{
  "data": [
    ...
  ],
  "links": {
    "first": "http://shopper.test/api/items?page[size]=2&page[number]=1",
    "last": "http://shopper.test/api/items?page[size]=2&page[number]=5",
    "prev": "http://shopper.test/api/items?page[size]=2&page[number]=2",
    "next": "http://shopper.test/api/items?page[size]=2&page[number]=4"
  },
  "meta": {
    "current_page": 3,
    "from": 5,
    "last_page": 5,
    "path": "http://shopper.test/api/items",
    "per_page": "2",
    "to": 6,
    "total": 10
  }
}
```

<!-- theme: info -->
> Using the pagination parameters allows you to split up your data to improve the performance and navigability of the API, but it may not always be necessary.

#### Filtering, sorting and paginating at the same time

Just like combining filtering and sorting, you can also add pagination to the request to use all three at the same time. For example, you can get all items for a list or meal, sort them accordingly and paginate them to return a certain amount of items offset as you wish.

```
$ curl http://shopper.test/api/items?filter=list:1&sort=name&page[size]=2&page[number]=3 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json'
```

The response body returned from this request will give you results filtered for the list with ID 1, sorted by the item name in ascending order and only contain two items that are offset by three pages.

This is how the response body would look if you performed the above request and had a total of 10 items on the list.

<!-- lineNumbers: false -->
```json
{
  "data": [
    {
      "id": 5,
      "user_id": 1,
      "name": "Example Item 5",
      "created_at": "2020-06-01T12:00:00.000000Z",
      "updated_at": "2020-06-01T12:00:00.000000Z",
    },
    {
      "id": 6,
      "user_id": 1,
      "name": "Example Item 6",
      "created_at": "2020-06-01T12:00:00.000000Z",
      "updated_at": "2020-06-01T12:00:00.000000Z",
    }
  ],
  "links": {
    "first": "http://shopper.test/api/items?filter=list:1&sort=name&page[size]=2&page[number]=1",
    "last": "http://shopper.test/api/items?filter=list:1&sort=name&page[size]=2&page[number]=5",
    "prev": "http://shopper.test/api/items?filter=list:1&sort=name&page[size]=2&page[number]=2",
    "next": "http://shopper.test/api/items?filter=list:1&sort=name&page[size]=2&page[number]=4"
  },
  "meta": {
    "current_page": 3,
    "from": 5,
    "last_page": 5,
    "path": "http://shopper.test/api/items",
    "per_page": "2",
    "to": 6,
    "total": 10
  }
}
```

### Creating an item

To create an item send a `POST` request to the `/api/items` endpoint containing the name of the item in the request payload.

```
$ curl -X POST http://shopper.test/api/items \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Item 3"}'
```

As you can probably tell, we :heart: JSON, so your request payload should always be valid JSON. The actual payload, when formatted, looks like this.

<!-- lineNumbers: false -->
```json
{
  "name": "Example Item 3"
}
```

If your request succeeds, the new item will be returned in the response body.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 3,
    "user_id": 1,
    "name": "Example Item 3",
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
  -d '{"name": "Example Item 4", "list_id": 1}'
```

#### Creating an item for a specific meal

You also have the option of creating an item for a specific meal. To do so include the meal ID in the request payload along with the name of the item.

```
$ curl -X POST http://shopper.test/api/items \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Item 5", "meal_id": 1}'
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

The only difference in response to these scenarios is that if a new item is created a `201 Created` status is returned, otherwise, if the item already exists a `200 OK` status is returned.

<!-- theme: warning -->
> Please note, if `name` and `item_id` are both present in the same request the `item_id` will take precedence, meaning the existing item with the ID provided will be added to the list or meal.

### Updating an item

To update an item send a `PATCH` request to the `/api/items/{id}` endpoint containing the new name of the item in the request payload.

```
$ curl -X PATCH http://shopper.test/api/items/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Item 1 Updated"}'
```

If your request succeeds, the updated item will be returned in the response body.

<!-- lineNumbers: false -->
```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "Example Item 1 Updated",
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
  -d '{"name": "Example Item 1 Updated", "list_id": 1}'
```

#### Updating an item for a specific meal

You also have the option of updating an item for a specific meal. To do so include the meal ID in the request payload along with the new name of the item.

```
$ curl -X PATCH http://shopper.test/api/items/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"name": "Example Item 1 Updated", "meal_id": 1}'
```

When updating an item for a specific list or meal, if the item is used elsewhere on another list or meal, this needs to be preserved. Therefore, instead of updating the item, we detach it from the list or meal and [create](#creating-an-item) a new item to preserve this history. A `201 Created` status is returned in this scenario along with the new item in the response body.

If the item hasn't been used elsewhere, the item is updated and a `200 OK` status is returned along with the updated item in the response body.

### Deleting an item

Use the `DELETE` method to delete an item through the API. In this example we'll delete the item with ID `1`.

```
$ curl -X DELETE http://shopper.test/api/items/1 \
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

When deleting an item from a specific list or meal, if it is used elsewhere then this is preserved and it will remain an item on this list or meal.

For all `DELETE` requests a `204 No Content` status is returned with an empty response body.

### Completing an item on a list

To complete an item on a list send a `POST` request to the `/api/completed-items` endpoint.

You need to include the ID of the item you'd like to complete and the ID of the list this is on in the request payload.

```
$ curl -X POST http://shopper.test/api/completed-items \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"item_id": 1, "list_id": 1}'
```

This request will complete the item with an ID of `1` on the list with ID `1`. If your request succeeds, the item will be marked as completed and you will receive a `204 No Content` response.

### Incompleting an item on a list

If you would like to incomplete an item after it has been completed you can send a `DELETE` request to `/api/completed-items/{id}`.

You need to include the ID of the item you're incompleting in the URL and the list ID this is on in the request payload.

```
$ curl -X DELETE http://shopper.test/api/completed-items/1 \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"list_id": 1}'
```

If your request succeeds, the item will be marked as incomplete and you will receive a `204 No Content` response status.
