<?php

class Model {
    protected $db; // La base de datos que heredarán los otros modelos

    public function __construct() {
        // Se establece la conexión con la base de datos
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS);
        $this->deploy();
    }

    /**
     * Crea las tablas en la base de datos en caso que esté vacía
     */
    public function deploy() {
        // Se verifica si hay tablas en la DB
        $query = $this->db->query('SHOW TABLES');

        // Se obtienen todas las tablas de la DB
        $tables = $query->fetchAll();

        // Si no hay, se crean
        if (count($tables) == 0) {
            $sql = <<<END
            -- phpMyAdmin SQL Dump
            -- version 5.2.1
            -- https://www.phpmyadmin.net/
            --
            -- Servidor: 127.0.0.1
            -- Tiempo de generación: 13-11-2023 a las 18:15:55
            -- Versión del servidor: 10.4.28-MariaDB
            -- Versión de PHP: 8.2.4
            
            SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
            START TRANSACTION;
            SET time_zone = "+00:00";
            
            
            /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
            /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
            /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
            /*!40101 SET NAMES utf8mb4 */;
            
            --
            -- Base de datos: `web2_tpe`
            --
            
            -- --------------------------------------------------------
            
            --
            -- Estructura de tabla para la tabla `albums`
            --
            
            CREATE TABLE `albums` (
              `id` int(11) NOT NULL,
              `title` varchar(100) NOT NULL,
              `year` int(11) NOT NULL,
              `band_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            
            --
            -- Volcado de datos para la tabla `albums`
            --
            
            INSERT INTO `albums` (`id`, `title`, `year`, `band_id`) VALUES
            (1, 'Rock a Medianoche', 2000, 1),
            (2, 'Rock Eterno', 1999, 1),
            (3, 'Raíces sureñas', 1988, 2),
            (4, 'Folklore de la Pampa', 1999, 2),
            (5, 'Salsa y Pasión', 2005, 3),
            (6, 'Tropical Heat', 2012, 3),
            (7, 'Cachengue Bristol', 2016, 5),
            (8, 'Yo Quiero Rock', 2001, 6),
            (9, 'Con vos', 2004, 7),
            (10, 'Baila', 2010, 7),
            (11, '20 Grandes Éxitos', 2020, 8),
            (12, 'Tanto Tango', 1998, 9);
            
            -- --------------------------------------------------------
            
            --
            -- Estructura de tabla para la tabla `bands`
            --
            
            CREATE TABLE `bands` (
              `id` int(11) NOT NULL,
              `name` varchar(100) NOT NULL,
              `genre` varchar(100) NOT NULL,
              `formed_location` varchar(100) NOT NULL,
              `formed_year` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            
            --
            -- Volcado de datos para la tabla `bands`
            --
            
            INSERT INTO `bands` (`id`, `name`, `genre`, `formed_location`, `formed_year`) VALUES
            (1, 'Rockeros de la Noche', 'Rock', 'Mar del Plata', 1998),
            (2, 'Sonidos del Sur', 'Folklore', 'Bahía Blanca', 2003),
            (3, 'Hot Salsa Quilmes', 'Salsa', 'Quilmes', 2010),
            (4, 'Lomas de Milonga', 'Tango', 'Lomas de Zamora', 2008),
            (5, 'Cuartetazo Bristol', 'Cuarteto', 'Mar del Plata', 2015),
            (6, 'Esto es Rock', 'Rock', 'Tandil', 2000),
            (7, 'MDQumbia ', 'Cumbia', 'Mar del Plata', 2002),
            (8, 'Aires Sureños', 'Folklore', 'Bahía Blanca', 2012),
            (9, 'Dor por Cuatro', 'Tango', 'La Plata', 1995);
            
            -- --------------------------------------------------------
            
            --
            -- Estructura de tabla para la tabla `users`
            --
            
            CREATE TABLE `users` (
              `id` int(11) NOT NULL,
              `username` varchar(100) NOT NULL,
              `password` varchar(100) NOT NULL,
              `exp` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            
            --
            -- Volcado de datos para la tabla `users`
            --
            
            INSERT INTO `users` (`id`, `username`, `password`, `exp`) VALUES
            (1, 'webadmin', '$2y$10\$sG.FqhVNVlwjWZkSmgE6O.YT7Dxm94JtfoRjRxZsjaXqpWhw/GDwS', 0);
            
            --
            -- Índices para tablas volcadas
            --
            
            --
            -- Indices de la tabla `albums`
            --
            ALTER TABLE `albums`
              ADD PRIMARY KEY (`id`),
              ADD KEY `FK_band_id` (`band_id`);
            
            --
            -- Indices de la tabla `bands`
            --
            ALTER TABLE `bands`
              ADD PRIMARY KEY (`id`);
            
            --
            -- Indices de la tabla `users`
            --
            ALTER TABLE `users`
              ADD PRIMARY KEY (`id`),
              ADD UNIQUE KEY `UNIQUE_username` (`username`) USING BTREE;
            
            --
            -- AUTO_INCREMENT de las tablas volcadas
            --
            
            --
            -- AUTO_INCREMENT de la tabla `albums`
            --
            ALTER TABLE `albums`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
            
            --
            -- AUTO_INCREMENT de la tabla `bands`
            --
            ALTER TABLE `bands`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
            
            --
            -- AUTO_INCREMENT de la tabla `users`
            --
            ALTER TABLE `users`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
            COMMIT;
            
            /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
            /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
            /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
            END;

            $this->db->query($sql);
        }
    }
}
