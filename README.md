PHP.ApiRest.RoberthLoaiza

#### Results

Se realizo el CRUD completo de la entidad result, la cual posee
una llave foranea que enlaza a la tabla de usuarios. El controlador
sera el encargado de realizar las operaciones de GET, CGET, 
POST, PUT, OPTIONS y tambien se implemento la verificación de que los datos esten completos
para hacer un POST o PUT. De tal forma tambien se modifico el
archivo swagger.json para poder añadir el id de usuario en el 
modelo y tambien informacion del porque se lo implementa, por debajo
añadimos la fecha de esta forma al hacer un POST o PUT no lo
digitaremos manualmente. Donde el modelo final sera:

```
{
  "users_id": 1,
  "result": 100
}
```

#### Test
Se implemento los test tanto de la entidad Result como tambien
del Controlador, con esto se pudo verificar la covertura que tiene
la apliación rest.