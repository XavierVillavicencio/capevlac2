### levantar el contenedor:
docker compose up -d

### Apagar el contenedor:
docker compose down

### eliminar TODO, pq cambié la config:
docker rm -f $(docker ps -qa)
docker rmi -f $(docker images -aq)

 ### temas pendientes
 1. mysql 5.7 no funcionó, coloque el 8.0
 2. el archivo setup.sh le tuve q dar permisos 777 para q corra en a mac
 3. .htaccess faltaba, dejo demo acá mismo y agrego instrucciones en setup.sh para q de copiando
    1. cp htaccess ../../.htaccess  ¿¿¿donde ejecuto ???
 4. en application/config/database.php hay q cambiar localhost por el nombre del equipo, en mi caso 
    1. 'hostname' => 'kubernetes.docker.internal'
 5. elimino las otras carpetas docker para evitar confusiones 
 6. en el script de la base de datos agrego fecha de expiracion de la contraseña en el año 3000



 ### como para no olvidar:
 https://noviello.it/es/como-usar-el-comando-sed-para-buscar-y-reemplazar-cadenas-en-linux/

 ### generar el certificado https
 openssl req -x509 -newkey rsa:4096 -keyout flex.key -out flex.crt -sha256 -days 365