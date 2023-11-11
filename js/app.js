"use strict";

let albumList = document.querySelector("#album-list");
const URL = "http://localhost/web2/TPE-WEB2-3/api/albums/";

getAlbums();

async function getAlbums() {
    try {
        let response = await fetch(URL);
        if (!response.ok)
            throw new Error("El recurso no existe");
        let albums = await response.json();
        showAlbums(albums); // Album table rendering
    } catch (e) {
        console.log(e);
    }
}

function showAlbums(albums) {
    for (let i = 0; i < albums.length; i++) {
        albumList.innerHTML +=
            `<tr>  
                <td>${albums[i].id}</li>
                <td>${albums[i].title}</li>
                <td>${albums[i].year}</li>
                <td><a href="eliminar/${albums[i].id}">Eliminar</a></td>
            </tr>`
    }
}
