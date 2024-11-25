const API_KEY = "SECRET_API_KEY";
const DEBUG = true;

const debug = {
    log: function(message) {
        if (DEBUG) {
            const stack = new Error().stack;
            const caller = stack.split("\n")[2].trim();
            console.log(`DEBUG: ${message}, ${caller}`);
        }
    }
};

document.getElementById("getWeather").addEventListener("click", () => {
    const address = document.getElementById("address").value.trim();
    debug.log(`Address entered: ${address}`);
    if (!address) {
        alert("Please enter an address or city name.");
        return;
    }

    const geocodeAPI = `https://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(address)}&limit=1&appid=${API_KEY}`;
    debug.log(`Geocode API URL: ${geocodeAPI}`);

    fetch(geocodeAPI)
        .then((response) => {
            if (!response.ok) throw new Error("Error fetching geocode data.");
            return response.json();
        })
        .then((data) => {
            debug.log(`Geocode API response: ${JSON.stringify(data)}`);
            if (data.length === 0) {
                throw new Error("No location found.");
            }
            const { lat, lon } = data[0];
            debug.log(`Latitude: ${lat}, Longitude: ${lon}`);
            getWeatherData(lat, lon);
        })
        .catch((error) => {
            debug.log(`Error: ${error.message}`);
            alert(error.message);
        });
});

function getWeatherData(lat, lon) {
    const currentWeatherAPI = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${API_KEY}&units=metric`;
    const forecastAPI = `https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lon}&appid=${API_KEY}&units=metric`;

    debug.log(`Current Weather API URL: ${currentWeatherAPI}`);
    debug.log(`Forecast API URL: ${forecastAPI}`);

    // Fetch current weather using XMLHttpRequest
    const xhr = new XMLHttpRequest();
    xhr.open("GET", currentWeatherAPI, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const data = JSON.parse(xhr.responseText);
            debug.log(`Current Weather API response: ${JSON.stringify(data)}`);
            displayCurrentWeather(data);
        } else {
            debug.log("Error fetching current weather data.");
            alert("Error fetching current weather data.");
        }
    };
    xhr.send();

    // Fetch 5-day forecast using Fetch API
    fetch(forecastAPI)
        .then((response) => {
            if (!response.ok) throw new Error("Error fetching forecast data.");
            return response.json();
        })
        .then((data) => {
            debug.log(`Forecast API response: ${JSON.stringify(data)}`);
            displayForecast(data);
        })
        .catch((error) => {
            debug.log(`Error: ${error.message}`);
            alert(error.message);
        });
}

function getUtcOffset(timezoneOffset) {
    return `UTC${timezoneOffset >= 0 ? '+' : ''}${timezoneOffset / 3600}`;
}

function displayCurrentWeather(data) {
    const results = document.getElementById("results");
    const section = document.createElement("div");
    section.className = "weather-section";

    const utcTime = data.dt * 1000; // Convert to milliseconds
    const timezoneOffset = data.timezone;
    const utcOffset = getUtcOffset(timezoneOffset);
    const time = new Date(utcTime + timezoneOffset * 1000).toISOString().substring(0, 16).replace("T", " "); // Extract HH:MM from ISO string

    section.innerHTML = `
        <h2>Current Weather</h2>
        <div class="current-weather-container">
            <div class="current-weather-details">
                <p class="temperature">${data.main.temp} &deg;C</p>
                <p class="location">${data.name}</p>
                <p class="time">${time} (${utcOffset})</p>
            </div>
            <div class="current-weather-icon">
                <img src="https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png" alt="${data.weather[0].description}">
                <p class="weather">${data.weather[0].description.toUpperCase()}</p>
            </div>
        </div>
    `;
    results.innerHTML = ""; // Clear previous results
    results.appendChild(section);
    debug.log("Displayed current weather.");
}

function displayForecast(data) {
    const results = document.getElementById("results");
    const section = document.createElement("div");
    section.className = "forecast-section";
    section.innerHTML = "<h2>5-Day Forecast</h2>";

    const forecastByDay = data.list.reduce((acc, item) => {
        const date = item.dt_txt.split(" ")[0];
        if (!acc[date]) {
            acc[date] = [];
        }
        acc[date].push(item);
        return acc;
    }, {});

    const forecastContainer = document.createElement("div");
    forecastContainer.className = "forecast-container";

    Object.keys(forecastByDay).slice(0, 5).forEach((date) => {
        const daySection = document.createElement("div");
        daySection.className = "day-column";
        daySection.innerHTML = `<h3>${date}</h3>`;
        forecastByDay[date].forEach((item) => {
            const forecast = document.createElement("div");
            forecast.className = "forecast-item";
            const description = item.weather[0].description;
            const capitalizedDescription = description.charAt(0).toUpperCase() + description.slice(1);

            const utcTime = item.dt * 1000; // Convert to milliseconds
            const timezoneOffset = data.city.timezone; // Offset in seconds
            const localTime = new Date(utcTime + timezoneOffset * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const utcOffset = getUtcOffset(timezoneOffset);

            forecast.innerHTML = `
                <div class="forecast-details">
                    <div class="forecast-hour">${localTime} (${utcOffset})</div>
                    <div class="forecast-temp">${item.main.temp} &deg;C</div>
                    <div class="forecast-desc">${capitalizedDescription}</div>
                </div>
                <div class="forecast-icon">
                    <img src="http://openweathermap.org/img/wn/${item.weather[0].icon}@2x.png" alt="${description}">
                </div>
            `;
            daySection.appendChild(forecast);
        });
        forecastContainer.appendChild(daySection);
    });

    section.appendChild(forecastContainer);
    results.appendChild(section); // Append without clearing previous content
    debug.log("Displayed 5-day forecast.");
}

document.addEventListener("mousemove", (e) => {
    const cursor = document.querySelector(".blob");
    cursor.style.transform = `translate3d(calc(${e.pageX}px - 50%), calc(${e.pageY}px - 50%), 0)`;
});