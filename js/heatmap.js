/**
 * HEATMAP MODULE
 * Comprehensive heatmap visualization for AMU, MRL, and farm compliance data
 */

const HEATMAP_STATE = {
  map: null,
  markers: [],
  selectedRegion: "all",
  selectedMetric: "amu",
  timePeriod: "30days",
  heatmapData: [],
};

/**
 * Initialize Heatmap
 */
function initializeHeatmapVisualization() {
  console.log("🗺️ Initializing Heatmap Visualization");

  const mapContainer = document.getElementById("government-heatmap-container");
  if (!mapContainer) {
    console.warn("⚠️ Heatmap container not found");
    return;
  }

  // Initialize Leaflet map
  if (HEATMAP_STATE.map) {
    HEATMAP_STATE.map.remove();
  }

  HEATMAP_STATE.map = L.map("government-heatmap-container").setView(
    [20.5937, 78.9629],
    4
  );

  // Add tile layer
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors",
    maxZoom: 18,
  }).addTo(HEATMAP_STATE.map);

  // Add legend
  addHeatmapLegend();

  // Load and display heatmap data
  loadHeatmapData();

  console.log("✅ Heatmap initialized");
}

/**
 * Load heatmap data from API
 */
async function loadHeatmapData() {
  try {
    const params = new URLSearchParams({
      region: HEATMAP_STATE.selectedRegion,
      metric: HEATMAP_STATE.selectedMetric,
      time_period: HEATMAP_STATE.timePeriod,
    });

    const response = await fetch(`api/heatmap.php?${params}`);
    const result = await response.json();

    if (result.success) {
      HEATMAP_STATE.heatmapData = result.data || [];
      displayHeatmapMarkers();
      updateHeatmapStats(result);
      console.log("✅ Loaded heatmap data for " + result.count + " locations");
    }
  } catch (error) {
    console.error("❌ Error loading heatmap data:", error);
    showNotification("Error loading heatmap data", "error");
  }
}

/**
 * Display heatmap markers on map
 */
function displayHeatmapMarkers() {
  // Clear existing markers
  HEATMAP_STATE.markers.forEach((marker) => {
    if (HEATMAP_STATE.map.hasLayer(marker)) {
      HEATMAP_STATE.map.removeLayer(marker);
    }
  });
  HEATMAP_STATE.markers = [];

  HEATMAP_STATE.heatmapData.forEach((location) => {
    let color = "#10b981"; // green
    let intensity = 0;
    let label = "";

    if (HEATMAP_STATE.selectedMetric === "amu") {
      // AMU-based coloring
      const amuIndex = location.amu_index || 0;
      if (amuIndex > 0.8) {
        color = "#ef4444"; // red - high
        intensity = "high";
      } else if (amuIndex > 0.5) {
        color = "#f59e0b"; // orange - medium
        intensity = "medium";
      } else if (amuIndex > 0.2) {
        color = "#eab308"; // yellow - low
        intensity = "low";
      }
      label = `AMU Index: ${(amuIndex * 100).toFixed(1)}%`;
    } else if (HEATMAP_STATE.selectedMetric === "mrl") {
      // MRL compliance-based coloring
      const complianceRate = location.compliance_rate || 100;
      if (complianceRate < 80) {
        color = "#ef4444"; // red - non-compliant
      } else if (complianceRate < 95) {
        color = "#f59e0b"; // orange - partially compliant
      } else {
        color = "#10b981"; // green - compliant
      }
      label = `Compliance: ${complianceRate.toFixed(1)}%`;
    }

    // Create custom icon
    const customIcon = L.divIcon({
      className: "custom-heatmap-marker",
      html: `
                <div style="
                    background-color: ${color};
                    width: 35px;
                    height: 35px;
                    border-radius: 50%;
                    border: 3px solid white;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: bold;
                    cursor: pointer;
                ">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            `,
      iconSize: [41, 41],
      iconAnchor: [20, 41],
      popupAnchor: [1, -34],
    });

    const marker = L.marker([location.latitude, location.longitude], {
      icon: customIcon,
    }).addTo(HEATMAP_STATE.map);

    // Create popup content
    const popupContent = `
            <div class="p-3" style="min-width: 280px;">
                <h3 class="font-bold text-lg mb-2">${location.farm_name}</h3>
                <div class="mb-2">
                    <p class="text-sm"><strong>Farm ID:</strong> ${
                      location.farm_id
                    }</p>
                    <p class="text-sm"><strong>State:</strong> ${
                      location.state
                    }</p>
                </div>
                
                ${
                  HEATMAP_STATE.selectedMetric === "amu"
                    ? `
                    <div class="bg-gray-50 p-2 rounded mb-2">
                        <p class="text-sm"><strong>Prescriptions (30d):</strong> ${
                          location.prescription_count
                        }</p>
                        <p class="text-sm"><strong>AMU Index:</strong> ${(
                          location.amu_index * 100
                        ).toFixed(1)}%</p>
                        <p class="text-sm"><strong>Active Alerts:</strong> ${
                          location.alert_count
                        }</p>
                    </div>
                `
                    : `
                    <div class="bg-gray-50 p-2 rounded mb-2">
                        <p class="text-sm"><strong>Approved Tests:</strong> ${
                          location.approved_tests
                        }</p>
                        <p class="text-sm"><strong>Rejected Tests:</strong> ${
                          location.rejected_tests
                        }</p>
                        <p class="text-sm"><strong>Compliance Rate:</strong> <span class="font-bold text-${
                          location.compliance_rate >= 95 ? "green" : "red"
                        }-600">${location.compliance_rate.toFixed(
                        1
                      )}%</span></p>
                    </div>
                `
                }
                
                <div class="flex gap-2">
                    <button onclick="viewFarmDetails('${
                      location.farm_id
                    }')" class="text-xs bg-blue-600 text-white px-2 py-1 rounded">View Farm</button>
                    ${
                      location.alert_count > 0
                        ? `
                        <button onclick="viewFarmAlerts('${location.farm_id}')" class="text-xs bg-red-600 text-white px-2 py-1 rounded">Alerts (${location.alert_count})</button>
                    `
                        : ""
                    }
                </div>
            </div>
        `;

    marker.bindPopup(popupContent);
    marker.on("click", function () {
      this.openPopup();
    });

    HEATMAP_STATE.markers.push(marker);
  });

  console.log(
    `✅ Displayed ${HEATMAP_STATE.markers.length} markers on heatmap`
  );
}

/**
 * Add legend to heatmap
 */
function addHeatmapLegend() {
  const legend = L.control({ position: "bottomright" });

  legend.onAdd = function (map) {
    const div = L.DomUtil.create("div", "heatmap-legend");
    div.style.backgroundColor = "white";
    div.style.padding = "15px";
    div.style.borderRadius = "5px";
    div.style.boxShadow = "0 2px 8px rgba(0,0,0,0.2)";
    div.style.lineHeight = "1.5";
    div.style.maxWidth = "200px";

    if (HEATMAP_STATE.selectedMetric === "amu") {
      div.innerHTML = `
                <h4 style="margin: 0 0 10px 0; font-weight: bold; font-size: 14px;">AMU Intensity</h4>
                <div style="display: flex; align-items: center; margin-bottom: 5px;">
                    <span style="display: inline-block; width: 15px; height: 15px; background: #ef4444; border-radius: 50%; margin-right: 8px;"></span>
                    <span style="font-size: 12px;">High (>0.8)</span>
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 5px;">
                    <span style="display: inline-block; width: 15px; height: 15px; background: #f59e0b; border-radius: 50%; margin-right: 8px;"></span>
                    <span style="font-size: 12px;">Medium (0.5-0.8)</span>
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 5px;">
                    <span style="display: inline-block; width: 15px; height: 15px; background: #eab308; border-radius: 50%; margin-right: 8px;"></span>
                    <span style="font-size: 12px;">Low (0.2-0.5)</span>
                </div>
                <div style="display: flex; align-items: center;">
                    <span style="display: inline-block; width: 15px; height: 15px; background: #10b981; border-radius: 50%; margin-right: 8px;"></span>
                    <span style="font-size: 12px;">Minimal (<0.2)</span>
                </div>
            `;
    } else {
      div.innerHTML = `
                <h4 style="margin: 0 0 10px 0; font-weight: bold; font-size: 14px;">MRL Compliance</h4>
                <div style="display: flex; align-items: center; margin-bottom: 5px;">
                    <span style="display: inline-block; width: 15px; height: 15px; background: #10b981; border-radius: 50%; margin-right: 8px;"></span>
                    <span style="font-size: 12px;">Compliant (≥95%)</span>
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 5px;">
                    <span style="display: inline-block; width: 15px; height: 15px; background: #f59e0b; border-radius: 50%; margin-right: 8px;"></span>
                    <span style="font-size: 12px;">Partial (80-95%)</span>
                </div>
                <div style="display: flex; align-items: center;">
                    <span style="display: inline-block; width: 15px; height: 15px; background: #ef4444; border-radius: 50%; margin-right: 8px;"></span>
                    <span style="font-size: 12px;">Non-Compliant (<80%)</span>
                </div>
            `;
    }

    return div;
  };

  legend.addTo(HEATMAP_STATE.map);
}

/**
 * Update heatmap statistics
 */
function updateHeatmapStats(data) {
  const statsContainer = document.getElementById("heatmap-stats-container");
  if (!statsContainer) return;

  let statsHtml = `
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-600">Total Locations</p>
                <p class="text-3xl font-bold text-primary">${data.count}</p>
            </div>
    `;

  if (HEATMAP_STATE.selectedMetric === "amu") {
    const highRisk = data.data.filter((d) => d.amu_index > 0.8).length;
    const mediumRisk = data.data.filter(
      (d) => d.amu_index > 0.5 && d.amu_index <= 0.8
    ).length;

    statsHtml += `
            <div class="bg-red-50 p-4 rounded-lg shadow">
                <p class="text-sm text-red-600">High AMU</p>
                <p class="text-3xl font-bold text-red-600">${highRisk}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg shadow">
                <p class="text-sm text-yellow-600">Medium AMU</p>
                <p class="text-3xl font-bold text-yellow-600">${mediumRisk}</p>
            </div>
        `;
  } else {
    const compliant = data.data.filter((d) => d.compliance_rate >= 95).length;
    const nonCompliant = data.data.filter((d) => d.compliance_rate < 80).length;

    statsHtml += `
            <div class="bg-green-50 p-4 rounded-lg shadow">
                <p class="text-sm text-green-600">Compliant</p>
                <p class="text-3xl font-bold text-green-600">${compliant}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg shadow">
                <p class="text-sm text-red-600">Non-Compliant</p>
                <p class="text-3xl font-bold text-red-600">${nonCompliant}</p>
            </div>
        `;
  }

  statsHtml += "</div>";
  statsContainer.innerHTML = statsHtml;
}

/**
 * Apply heatmap filters
 */
function applyHeatmapFilters() {
  const region =
    document.getElementById("heatmap-region-select")?.value || "all";
  const metric =
    document.getElementById("heatmap-metric-select")?.value || "amu";
  const period =
    document.getElementById("heatmap-period-select")?.value || "30days";

  HEATMAP_STATE.selectedRegion = region;
  HEATMAP_STATE.selectedMetric = metric;
  HEATMAP_STATE.timePeriod = period;

  loadHeatmapData();
}

/**
 * View farm details
 */
function viewFarmDetails(farmId) {
  showNotification(`📍 Viewing details for farm ${farmId}`, "info");
  // Navigate to farm details view
}

/**
 * View farm alerts
 */
function viewFarmAlerts(farmId) {
  showNotification(`⚠️ Viewing alerts for farm ${farmId}`, "warning");
  // Navigate to farm alerts view
}

/**
 * Export heatmap data
 */
function exportHeatmapData() {
  const data = {
    metric: HEATMAP_STATE.selectedMetric,
    region: HEATMAP_STATE.selectedRegion,
    period: HEATMAP_STATE.timePeriod,
    locations: HEATMAP_STATE.heatmapData,
    exportedAt: new Date().toISOString(),
  };

  const csv = convertToCSV(data.locations);
  downloadCSV(csv, `heatmap_${HEATMAP_STATE.selectedMetric}_${Date.now()}.csv`);
  showNotification("📥 Heatmap data exported", "success");
}

/**
 * Convert data to CSV
 */
function convertToCSV(data) {
  if (!data || data.length === 0) return "";

  const headers = Object.keys(data[0]);
  const rows = data.map((row) =>
    headers.map((header) => row[header]).join(",")
  );
  return [headers.join(","), ...rows].join("\n");
}

/**
 * Download CSV file
 */
function downloadCSV(csv, filename) {
  const blob = new Blob([csv], { type: "text/csv" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  window.URL.revokeObjectURL(url);
  document.body.removeChild(a);
}

// Export for use in HTML
window.initializeHeatmapVisualization = initializeHeatmapVisualization;
window.applyHeatmapFilters = applyHeatmapFilters;
window.viewFarmDetails = viewFarmDetails;
window.viewFarmAlerts = viewFarmAlerts;
window.exportHeatmapData = exportHeatmapData;

console.log("✅ Heatmap Module loaded");
