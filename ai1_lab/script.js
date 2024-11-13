const DEBUG = true; // Set to false to disable debug logs

function logDebug(message) {
  if (DEBUG) {
    const stack = new Error().stack;
    const caller = stack.split("\n")[2].trim();
    console.log(`DEBUG: ${message}, ${caller}`);
  }
}

class MapPuzzle {
  constructor(
    mapId,
    downloadButtonId,
    puzzleContainerId,
    puzzleBoardId,
    mapCanvasId,
    localizationButtonId
  ) {
    this.isDownloading = false;
    this.map = L.map(mapId).setView([53.4285, 14.5528], 13);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(
      this.map
    );

    this.puzzleContainer = document.getElementById(puzzleContainerId);
    this.puzzleBoard = document.getElementById(puzzleBoardId);
    this.mapCanvas = document.getElementById(mapCanvasId);
    this.userLocation = null;
    this.userMarker = null;
    this.notificationsEnabled = false;

    this.requestNotificationPermission();

    document.getElementById(downloadButtonId).addEventListener("click", () => {
      if (this.isDownloading) {
        logDebug("Download is already in progress.");
        return;
      }

      this.isDownloading = true;
      logDebug("Starting download process...");
      this.generatePuzzlePieces();
    });

    document
      .getElementById(localizationButtonId)
      .addEventListener("click", () => {
        this.setLocalization();
      });

    this.puzzleBoard.addEventListener("dragover", this.allowDrop);
    this.puzzleBoard.addEventListener("drop", this.drop.bind(this));
  }

  requestNotificationPermission() {
    if ("Notification" in window) {
      Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
          this.notificationsEnabled = true;
          logDebug("Notifications enabled.");
        } else {
          logDebug("Notifications denied.");
        }
      });
    } else {
      logDebug("Notifications are not supported by this browser.");
    }
  }

  showNotification(message) {
    if (this.notificationsEnabled) {
      new Notification(message);
      logDebug(`Notification shown: ${message}`);
    } else {
      logDebug(`Notification not shown: ${message}`);
    }
  }

  clearPuzzleContainer() {
    this.puzzleContainer.innerHTML = "";
    logDebug("Cleared puzzle container.");
  }

  generatePuzzlePieces() {
    leafletImage(this.map, (err, canvas) => {
      if (err) {
        console.error("Error generating map image:", err);
        this.isDownloading = false;
        return;
      }

      this.clearPuzzleContainer();

      const imageURL = canvas.toDataURL();
      this.createPuzzle(imageURL, canvas);

      this.drawMapOnCanvas(canvas);

      this.isDownloading = false;
      logDebug("Download process completed.");
    });
  }

  drawMapOnCanvas(canvas) {
    const mapCanvasContext = this.mapCanvas.getContext("2d");
    this.mapCanvas.width = canvas.width;
    this.mapCanvas.height = canvas.height;
    mapCanvasContext.drawImage(canvas, 0, 0);
  }

  createPuzzle(imageURL, canvas) {
    logDebug("Creating puzzle pieces...");
    const pieces = [];
    const pieceWidth = canvas.width / 4;
    const pieceHeight = canvas.height / 4;

    for (let i = 0; i < 16; i++) {
      const piece = document.createElement("div");
      piece.classList.add("puzzle-piece");
      piece.style.width = `${pieceWidth}px`;
      piece.style.height = `${pieceHeight}px`;
      piece.style.backgroundImage = `url(${imageURL})`;
      piece.style.backgroundPosition = `${-(i % 4) * pieceWidth}px ${
        -Math.floor(i / 4) * pieceHeight
      }px`;
      piece.style.backgroundSize = `${canvas.width}px ${canvas.height}px`;
      piece.draggable = true;
      piece.id = "piece-" + i;

      piece.addEventListener("dragstart", this.dragStart.bind(this));
      piece.addEventListener("dragend", this.dragEnd);
      piece.addEventListener("dblclick", this.doubleClick.bind(this));
      pieces.push(piece);
    }

    this.shuffleArray(pieces);
    pieces.forEach((piece) => this.puzzleContainer.appendChild(piece));

    this.createPuzzleBoard();
  }

  shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [array[i], array[j]] = [array[j], array[i]];
    }
  }

  dragStart(event) {
    event.dataTransfer.setData("text/plain", event.target.id);
    event.dataTransfer.effectAllowed = "move";
    logDebug(`Drag started: ${event.target.id}`);

    const parentCell = event.target.parentElement;
    if (parentCell && parentCell.classList.contains("puzzle-board-cell")) {
      parentCell.classList.remove("occupied");
    }
  }

  dragEnd(event) {
    logDebug(`Drag ended: ${event.target.id}`);
  }

  allowDrop(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = "move";
  }

  drop(event) {
    event.preventDefault();
    const pieceId = event.dataTransfer.getData("text/plain");
    const piece = document.getElementById(pieceId);

    if (
      event.target.classList.contains("puzzle-board-cell") &&
      !event.target.classList.contains("occupied")
    ) {
      event.target.appendChild(piece);
      event.target.classList.add("occupied");
      logDebug(
        `Piece ${pieceId} dropped on cell ${event.target.dataset.index}.`
      );

      const cellIndex = parseInt(event.target.dataset.index, 10);
      const pieceIndex = parseInt(pieceId.split("-")[1], 10);
      if (cellIndex === pieceIndex) {
        logDebug(`Piece ${pieceId} is in the correct position.`);
        this.checkIfPuzzleSolved();
      } else {
        logDebug(`Piece ${pieceId} is not in the correct position.`);
      }
    } else {
      logDebug(`Invalid drop target or target is occupied.`);
    }
  }

  createPuzzleBoard() {
    this.puzzleBoard.innerHTML = "";

    for (let i = 0; i < 16; i++) {
      const cell = document.createElement("div");
      cell.classList.add("puzzle-board-cell");
      cell.dataset.index = i;
      this.puzzleBoard.appendChild(cell);
    }
  }

  checkIfPuzzleSolved() {
    const cells = this.puzzleBoard.querySelectorAll(".puzzle-board-cell");
    let isSolved = true;

    cells.forEach((cell, index) => {
      const piece = cell.firstChild;

      if (!piece || piece.id !== `piece-${index}`) {
        isSolved = false;
      }
    });

    if (isSolved) {
      this.showNotification("Congratulations! You solved the puzzle!");

      setTimeout(() => {
        alert("Congratulations! You solved the puzzle!");
      }, 300);

      logDebug("Puzzle solved!");
    }
  }

  doubleClick(event) {
    const piece = event.target;
    const parentCell = piece.parentElement;
    if (parentCell && parentCell.classList.contains("puzzle-board-cell")) {
      parentCell.classList.remove("occupied");
    }
    this.puzzleContainer.appendChild(piece);
    logDebug(`Piece ${piece.id} moved back to puzzle container.`);
  }

  setLocalization() {
    if (!this.userLocation) {
      this.getLocalization();
    } else {
      this.map.setView(this.userLocation, 14);
      this.addMarker(this.userLocation);
      logDebug(`Map set to current location: [${this.userLocation}]`);
    }
  }

  getLocalization() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const { latitude, longitude } = position.coords;
          this.userLocation = [latitude, longitude];
          this.map.setView(this.userLocation, 14);
          this.addMarker(this.userLocation);
          logDebug(`Map set to current location: [${this.userLocation}]`);
        },
        (error) => {
          console.error("Error getting location:", error);
        }
      );
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  }

  addMarker(location) {
    if (this.userMarker) {
      this.map.removeLayer(this.userMarker);
    }
    this.userMarker = L.marker(location).addTo(this.map);
    logDebug(`Marker added at location: [${location[0]}, ${location[1]}]`);
  }
}

const mapPuzzle = new MapPuzzle(
  "map",
  "download-map",
  "puzzle-container",
  "puzzle-board",
  "map-canvas",
  "my-localization"
);

document.addEventListener("mousemove", (e) => {
  const cursor = document.querySelector(".blob");
  cursor.style.transform = `translate3d(calc(${e.clientX}px - 50%), calc(${e.clientY}px - 50%), 0)`;
});
