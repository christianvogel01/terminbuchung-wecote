const navigationEntry = performance.getEntriesByType("navigation")[0];

if (navigationEntry && navigationEntry.type === "reload") {
  navigator.sendBeacon("auto_logout.php");
  window.location.href = "index.php";
}
