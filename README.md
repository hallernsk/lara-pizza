### Эндпойнты API:

#### Регистрация/авторизация (JWT-токен, пакет tymon/jwt-auth):

POST:/api/auth/register  

POST:/api/auth/login  

POST:/api/auth/logout  


#### Товары (для всех):

GET:/api/   - (INDEX)  

GET:/api/products	- (INDEX)  

GET:/api/products/{i} - (SHOW)  


#### Товары (для админа):

GET:api/admin/    - (INDEX)  

GET:api/admin/products   - (INDEX)  

GET:api/admin/products/{i}   - (SHOW)  

POST:api/admin/products   - (STORE)  

PUT:api/admin/products/{i}   - (UPDATE)  

DELETE:api/admin/products/{i}   - (DESTROY)  


#### Корзина:
GET:api/cart/   - (SHOW)  

POST:api/cart/   - (STORE)  

PATCH:api/cart/{i}   - (UPDATE)  

DELETE:api/cart/{i}   - (DESTROY)

#### Заказы (для авторизованных пользователей):
GET:api/orders/   - (INDEX)  

GET:api/orders/{i}   - (SHOW)  

POST:api/orders/   - (UPDATE)  


#### Заказы (для админа):
GET:api/admin/orders/   - (INDEX)  

GET:api/admin/orders/{i}   - (SHOW)  

PUT:api/admin/orders/   - (UPDATE)  




