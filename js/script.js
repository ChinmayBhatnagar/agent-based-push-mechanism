// Function to send user activity to the server (PHP backend)
function sendActivity(activityType, activityData) {
    if (!activityData.trim()) {
        document.getElementById("suggestions").innerHTML = ""; // Clear suggestions if empty
        return;
    }

    fetch(`activity.php?activity_type=${activityType}&activity_data=${encodeURIComponent(activityData)}`)
    .then(response => response.json())
    .then(data => {
        // Check if response contains a message or valid results
        if (!data || (typeof data === "object" && "message" in data)) {
            document.getElementById("suggestions").innerHTML = `<p>${data.message || "No relevant information found."}</p>`;
            return;
        }

        // Ensure data is an array before displaying suggestions
        if (Array.isArray(data) && data.length > 0) {
            displaySuggestions(data);
        } else {
            document.getElementById("suggestions").innerHTML = `<p>No relevant information found.</p>`;
        }
    })
    .catch(error => console.error("Error:", error));
}

// Function to display relevant document suggestions
function displaySuggestions(items) {
    const suggestionsDiv = document.getElementById("suggestions");
    suggestionsDiv.innerHTML = "<h3>Suggested Resources:</h3>"; // Clear before updating

    items.forEach(item => {
        let content = "";
        if (item.type === "document") {
            content = `<p><strong>${item.title}</strong><br>
                      <a href="${item.link}" target="_blank">${item.link}</a><br>
                      <em>Category: ${item.category}</em></p>`;
        } else if (item.type === "video") {
            content = `<p><strong>${item.title}</strong><br>
                      <a href="${item.link}" target="_blank">Watch Video</a><br>
                      <em>Category: ${item.category}</em></p>`;
        } else if (item.type === "image") {
            content = `<p><strong>${item.title}</strong><br>
                      <img src="${item.link}" alt="${item.title}" 
                      style="max-width:200px; display:block;"><br>
                      <em>Category: ${item.category}</em></p>`;
        }                
        suggestionsDiv.innerHTML += content;
    });
}

// Function to save search queries in the database with debouncing
let searchTimeout; // Timeout variable for debounce
function saveSearchQuery(searchQuery) {
    if (!searchQuery.trim()) return; // Ignore empty search queries

    clearTimeout(searchTimeout); // Clear previous timeout if user is still typing

    searchTimeout = setTimeout(() => {
        fetch('save_search.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `search_query=${encodeURIComponent(searchQuery)}`
        })
        .catch(error => console.error("Error saving search query:", error));
    }, 500); // Wait 500ms before saving
}

// Event listener for typing activity
document.getElementById("user-activity").addEventListener("input", function () {
    sendActivity("typing", this.value);
});

// Event listener for search activity (saves search history with debounce)
document.getElementById("search-box").addEventListener("input", function () {
    let searchQuery = this.value;
    sendActivity("searching", searchQuery);
    saveSearchQuery(searchQuery);
});
