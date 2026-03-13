# 🪨📄✂️ Piedra, Papel o Tijera

Juego clásico de Piedra, Papel o Tijera con marcador persistente guardado en base de datos.

## ✨ Características

- Juega contra la máquina (elige aleatoriamente)
- Marcador persistente con victorias, empates y derrotas
- Historial de las últimas 5 partidas
- Diseño colorido y responsive (funciona en móvil)
- Comunicación asíncrona con AJAX (sin recargar la página)

## 🛠️ Tecnologías usadas

| Tecnología | Uso |
|------------|-----|
| HTML5      | Estructura de la página |
| CSS3       | Diseño y animaciones |
| JavaScript (Fetch API) | Comunicación con el servidor sin recargar |
| PHP 8+     | Lógica del juego y acceso a la base de datos |
| MySQL      | Guardar el historial de partidas |

## 📁 Estructura del proyecto

```
piedra-papel-tijera/
├── index.php           → Página principal del juego
├── setup.sql           → Script para crear la base de datos
├── config/
│   └── db.php          → Configuración de la conexión a MySQL
├── api/
│   └── game.php        → API que procesa cada partida
└── assets/
    └── css/
        └── style.css   → Estilos del juego
```

## 🚀 Instalación

### Requisitos previos
- XAMPP, WAMP o cualquier servidor local con PHP y MySQL

### Pasos

1. **Clona o descarga** este repositorio en la carpeta `htdocs` (XAMPP) o `www` (WAMP):
   ```
   git clone https://github.com/tu-usuario/piedra-papel-tijera.git
   ```

2. **Crea la base de datos**: Abre phpMyAdmin y ejecuta el archivo `setup.sql`

3. **Configura la conexión**: Edita `config/db.php` con tus credenciales:
   ```php
   define('DB_USER', 'root');   // Tu usuario
   define('DB_PASS', '');       // Tu contraseña
   ```

4. **Abre el navegador** y accede a:
   ```
   http://localhost/piedra-papel-tijera/
   ```

## 🎮 Cómo jugar

1. Haz clic en **Piedra**, **Papel** o **Tijera**
2. La máquina elige aleatoriamente
3. Se muestra el resultado y se guarda en la base de datos
4. El marcador se actualiza automáticamente

## 📸 Captura de pantalla



## 👨‍💻 Autor

Desarrollado como proyecto de aprendizaje para el ciclo **DAW (Desarrollo de Aplicaciones Web)**.
