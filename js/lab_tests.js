/**
 * LAB TESTS AND HEATMAP MODULE
 * Comprehensive functionality for lab tests, samples, reports, and heatmap visualization
 */

const LAB_TEST_STATE = {
  tests: [],
  samples: [],
  reports: [],
  selectedTest: null,
  heatmapInstance: null,
};

const MRL_STANDARDS = {
  Oxytetracycline: {
    milk: 0.1,
    meat: 0.2,
    description: "Broad-spectrum antibiotic",
  },
  Amoxicillin: {
    milk: 0.075,
    meat: 0.1,
    description: "Penicillin-based antibiotic",
  },
  Enrofloxacin: {
    milk: 0.1,
    meat: 0.15,
    description: "Fluoroquinolone antibiotic",
  },
  Gentamicin: {
    milk: 0.2,
    meat: 0.3,
    description: "Aminoglycoside antibiotic",
  },
  Streptomycin: {
    milk: 0.2,
    meat: 0.5,
    description: "Tuberculosis antibiotic",
  },
  Tetracycline: {
    milk: 0.1,
    meat: 0.2,
    description: "Broad-spectrum antibiotic",
  },
  Chloramphenicol: {
    milk: 0.03,
    meat: 0.05,
    description: "Broad-spectrum antibiotic (restricted)",
  },
  Sulfonamides: { milk: 0.1, meat: 0.3, description: "Sulfa drug" },
};

/**
 * Initialize Lab Tests Module
 */
function initLabTestsModule() {
  console.log("🧪 Initializing Lab Tests Module");
  loadLabTests();
  loadLabReports();
  setupLabEventListeners();
}

/**
 * Setup event listeners for lab test UI
 */
function setupLabEventListeners() {
  // Lab test form submission
  const labTestForm = document.getElementById("create-lab-test-form");
  if (labTestForm) {
    labTestForm.addEventListener("submit", handleCreateLabTest);
  }

  // Sample collection form
  const sampleForm = document.getElementById("sample-collection-form");
  if (sampleForm) {
    sampleForm.addEventListener("submit", handleSampleCollection);
  }

  // Report generation form
  const reportForm = document.getElementById("generate-report-form");
  if (reportForm) {
    reportForm.addEventListener("submit", handleGenerateReport);
  }

  // Lab test filter
  const labFilter = document.getElementById("lab-test-filter");
  if (labFilter) {
    labFilter.addEventListener("change", filterLabTests);
  }
}

/**
 * Load lab tests from API
 */
async function loadLabTests() {
  try {
    const response = await fetch("api/lab_tests.php");
    const result = await response.json();

    if (result.success) {
      LAB_TEST_STATE.tests = result.data || [];
      console.log("✅ Loaded " + LAB_TEST_STATE.tests.length + " lab tests");
      displayLabTests();
      return LAB_TEST_STATE.tests;
    }
  } catch (error) {
    console.error("❌ Error loading lab tests:", error);
  }
  return [];
}

/**
 * Load lab reports from API
 */
async function loadLabReports() {
  try {
    const response = await fetch("api/lab_test_reports.php");
    const result = await response.json();

    if (result.success) {
      LAB_TEST_STATE.reports = result.data || [];
      console.log(
        "✅ Loaded " + LAB_TEST_STATE.reports.length + " lab reports"
      );
      displayLabReports();
      return LAB_TEST_STATE.reports;
    }
  } catch (error) {
    console.error("❌ Error loading lab reports:", error);
  }
  return [];
}

/**
 * Create a new lab test
 */
async function handleCreateLabTest(e) {
  e.preventDefault();

  const farmId = document.getElementById("lab-farm-id").value;
  const animalId = document.getElementById("lab-animal-id").value;
  const testType = document.getElementById("lab-test-type").value;
  const description = document.getElementById("lab-test-description").value;
  const priority = document.getElementById("lab-priority").value;

  if (!farmId || !testType) {
    showNotification("Please fill in all required fields", "error");
    return;
  }

  try {
    const response = await fetch("api/lab_tests.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        farm_id: farmId,
        animal_id: animalId,
        test_type: testType,
        description: description,
        priority: priority,
        vet_id: currentUser?.vet_id || "VET001",
      }),
    });

    const result = await response.json();

    if (result.success) {
      showNotification("✅ Lab test created successfully", "success");
      document.getElementById("create-lab-test-form").reset();
      await loadLabTests();
      LAB_TEST_STATE.selectedTest = result.id;
      showLabTestSection("sample-collection");
    } else {
      showNotification(
        "❌ Failed to create lab test: " + result.message,
        "error"
      );
    }
  } catch (error) {
    showNotification("❌ Error creating lab test: " + error.message, "error");
  }
}

/**
 * Handle sample collection
 */
async function handleSampleCollection(e) {
  e.preventDefault();

  if (!LAB_TEST_STATE.selectedTest) {
    showNotification("Please select or create a lab test first", "error");
    return;
  }

  const sampleType = document.getElementById("sample-type-collection").value;
  const collectionDate = document.getElementById("collection-date").value;
  const collectorName = document.getElementById("collector-name").value;
  const quantity = document.getElementById("sample-quantity").value;
  const unit = document.getElementById("sample-unit").value;

  if (!sampleType || !collectionDate) {
    showNotification("Please fill in all required fields", "error");
    return;
  }

  try {
    const response = await fetch("api/lab_test_samples.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        lab_test_id: LAB_TEST_STATE.selectedTest,
        sample_type: sampleType,
        collection_date: collectionDate,
        collector_name: collectorName,
        quantity: quantity,
        unit: unit,
      }),
    });

    const result = await response.json();

    if (result.success) {
      showNotification("✅ Sample collected: " + result.sample_id, "success");
      document.getElementById("sample-collection-form").reset();
      showLabTestSection("report-generation");
    } else {
      showNotification(
        "❌ Failed to collect sample: " + result.message,
        "error"
      );
    }
  } catch (error) {
    showNotification("❌ Error collecting sample: " + error.message, "error");
  }
}

/**
 * Handle report generation
 */
async function handleGenerateReport(e) {
  e.preventDefault();

  if (!LAB_TEST_STATE.selectedTest) {
    showNotification("Please select a lab test first", "error");
    return;
  }

  const labName = document.getElementById("report-lab-name").value;
  const technician = document.getElementById("report-technician").value;
  const testResults = getTestResults();

  if (testResults.length === 0) {
    showNotification("Please add at least one test result", "error");
    return;
  }

  try {
    // Get the sample ID
    const sampleId = LAB_TEST_STATE.selectedTest; // In real scenario, get from selected sample

    const response = await fetch("api/lab_test_reports.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        lab_test_id: LAB_TEST_STATE.selectedTest,
        sample_id: sampleId,
        lab_name: labName,
        technician: technician,
        test_results: testResults,
      }),
    });

    const result = await response.json();

    if (result.success) {
      showNotification(
        "✅ Lab report generated! MRL Status: " + result.mrl_status,
        "success"
      );
      displayLabReportPreview(result);
      showLabTestSection("report-review");
      await loadLabReports();
    } else {
      showNotification(
        "❌ Failed to generate report: " + result.message,
        "error"
      );
    }
  } catch (error) {
    showNotification("❌ Error generating report: " + error.message, "error");
  }
}

/**
 * Get test results from form
 */
function getTestResults() {
  const resultsContainer = document.getElementById("test-results-container");
  const results = [];

  if (!resultsContainer) return results;

  const resultEntries = resultsContainer.querySelectorAll(".test-result-entry");
  resultEntries.forEach((entry) => {
    const chemical = entry.querySelector('[name="chemical"]')?.value;
    const detectedValue = entry.querySelector('[name="detected_value"]')?.value;
    const mrlLimit = entry.querySelector('[name="mrl_limit"]')?.value;

    if (chemical && detectedValue && mrlLimit) {
      results.push({
        chemical: chemical,
        detected_value: parseFloat(detectedValue),
        mrl_limit: parseFloat(mrlLimit),
        status:
          parseFloat(detectedValue) <= parseFloat(mrlLimit)
            ? "compliant"
            : "non-compliant",
      });
    }
  });

  return results;
}

/**
 * Add test result entry
 */
function addTestResultEntry() {
  const container = document.getElementById("test-results-container");
  if (!container) return;

  const entry = document.createElement("div");
  entry.className = "test-result-entry p-4 border rounded-lg bg-gray-50 mb-3";
  entry.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Chemical</label>
                <select name="chemical" class="w-full px-3 py-2 border rounded-lg" required>
                    <option value="">Select Chemical</option>
                    ${Object.keys(MRL_STANDARDS)
                      .map((chem) => `<option value="${chem}">${chem}</option>`)
                      .join("")}
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Detected Value (mg/kg)</label>
                <input type="number" step="0.001" name="detected_value" class="w-full px-3 py-2 border rounded-lg" placeholder="0.05" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">MRL Limit (mg/kg)</label>
                <input type="number" step="0.001" name="mrl_limit" class="w-full px-3 py-2 border rounded-lg" placeholder="0.1" required>
            </div>
        </div>
        <button type="button" class="text-red-600 text-sm" onclick="this.parentElement.remove()">Remove</button>
    `;

  container.appendChild(entry);
}

/**
 * Display lab tests in table
 */
function displayLabTests() {
  const tableBody = document.getElementById("lab-tests-table-body");
  if (!tableBody) return;

  tableBody.innerHTML = "";

  LAB_TEST_STATE.tests.forEach((test) => {
    const row = document.createElement("tr");
    row.className = "border-b hover:bg-gray-50";

    const statusColor =
      {
        pending: "bg-yellow-100 text-yellow-800",
        sample_collected: "bg-blue-100 text-blue-800",
        report_generated: "bg-purple-100 text-purple-800",
        approved: "bg-green-100 text-green-800",
        rejected: "bg-red-100 text-red-800",
      }[test.status] || "bg-gray-100 text-gray-800";

    row.innerHTML = `
            <td class="px-6 py-4">${test.farm_id}</td>
            <td class="px-6 py-4">${test.animal_id || "-"}</td>
            <td class="px-6 py-4">${test.test_type}</td>
            <td class="px-6 py-4"><span class="px-2 py-1 rounded text-xs ${statusColor}">${
      test.status
    }</span></td>
            <td class="px-6 py-4 text-sm">${
              test.created_at?.split(" ")[0] || "-"
            }</td>
            <td class="px-6 py-4">
                <button onclick="selectLabTest(${
                  test.id
                })" class="action-btn btn-view text-xs">View</button>
                <button onclick="editLabTest(${
                  test.id
                })" class="action-btn btn-edit text-xs ml-1">Edit</button>
            </td>
        `;
    tableBody.appendChild(row);
  });
}

/**
 * Display lab reports
 */
function displayLabReports() {
  const container = document.getElementById("lab-reports-list");
  if (!container) return;

  container.innerHTML = "";

  LAB_TEST_STATE.reports.forEach((report) => {
    const mrlStatusClass =
      report.mrl_status === "approved"
        ? "text-green-600 font-bold"
        : report.mrl_status === "rejected"
        ? "text-red-600 font-bold"
        : "text-yellow-600";

    const card = document.createElement("div");
    card.className = "p-4 border rounded-lg bg-white shadow-sm mb-4";

    const testResults =
      typeof report.test_results === "string"
        ? JSON.parse(report.test_results)
        : report.test_results || [];

    card.innerHTML = `
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h4 class="font-bold text-lg">Lab Report #${report.id}</h4>
                    <p class="text-sm text-gray-600">Farm: ${
                      report.farm_id || "N/A"
                    }</p>
                    <p class="text-sm text-gray-600">Date: ${
                      report.report_date || report.created_at?.split(" ")[0]
                    }</p>
                </div>
                <span class="${mrlStatusClass} text-lg">${report.mrl_status?.toUpperCase()}</span>
            </div>
            
            <div class="mb-3">
                <h5 class="font-bold text-sm mb-2">Test Results:</h5>
                <div class="bg-gray-50 p-3 rounded-lg">
                    ${
                      testResults.length > 0
                        ? testResults
                            .map(
                              (r) => `
                        <div class="flex justify-between text-sm mb-2">
                            <span>${r.chemical}: ${
                                r.detected_value
                              } mg/kg</span>
                            <span class="${
                              r.status === "compliant"
                                ? "text-green-600"
                                : "text-red-600"
                            }">
                                ${r.status?.toUpperCase()}
                            </span>
                        </div>
                    `
                            )
                            .join("")
                        : "No test results"
                    }
                </div>
            </div>

            <div class="flex gap-2">
                <button onclick="viewLabReport(${
                  report.id
                })" class="action-btn btn-view text-xs">View Report</button>
                ${
                  report.mrl_status === "pending"
                    ? `
                    <button onclick="approveLabReport(${report.id})" class="action-btn btn-approve text-xs">Approve</button>
                    <button onclick="rejectLabReport(${report.id})" class="action-btn btn-delete text-xs">Reject</button>
                `
                    : ""
                }
                <button onclick="downloadLabReport(${
                  report.id
                })" class="action-btn btn-toggle text-xs">Download</button>
            </div>
        `;
    container.appendChild(card);
  });
}

/**
 * Approve lab report
 */
async function approveLabReport(reportId) {
  const notes = prompt("Enter approval notes:");
  if (notes === null) return;

  try {
    const response = await fetch("api/lab_test_reports.php", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: reportId,
        mrl_status: "approved",
        approval_notes: notes,
        approved_by: currentUser?.name || "Admin",
      }),
    });

    const result = await response.json();

    if (result.success) {
      showNotification("✅ Lab report approved", "success");
      await loadLabReports();
    } else {
      showNotification("❌ Failed to approve report", "error");
    }
  } catch (error) {
    showNotification("❌ Error: " + error.message, "error");
  }
}

/**
 * Reject lab report
 */
async function rejectLabReport(reportId) {
  const notes = prompt("Enter rejection reason:");
  if (notes === null) return;

  try {
    const response = await fetch("api/lab_test_reports.php", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: reportId,
        mrl_status: "rejected",
        approval_notes: notes,
        approved_by: currentUser?.name || "Admin",
      }),
    });

    const result = await response.json();

    if (result.success) {
      showNotification("⚠️ Lab report rejected", "warning");
      await loadLabReports();
    } else {
      showNotification("❌ Failed to reject report", "error");
    }
  } catch (error) {
    showNotification("❌ Error: " + error.message, "error");
  }
}

/**
 * Select a lab test
 */
function selectLabTest(testId) {
  LAB_TEST_STATE.selectedTest = testId;
  const test = LAB_TEST_STATE.tests.find((t) => t.id === testId);
  showNotification(`Selected test: ${test.test_type}`, "info");
}

/**
 * Filter lab tests
 */
function filterLabTests() {
  const filter = document.getElementById("lab-test-filter")?.value;
  if (!filter || filter === "all") {
    displayLabTests();
    return;
  }

  const filtered = LAB_TEST_STATE.tests.filter((t) => t.status === filter);
  const tableBody = document.getElementById("lab-tests-table-body");
  if (!tableBody) return;

  tableBody.innerHTML = "";
  filtered.forEach((test) => {
    const row = document.createElement("tr");
    row.innerHTML = `
            <td class="px-6 py-4">${test.farm_id}</td>
            <td class="px-6 py-4">${test.animal_id || "-"}</td>
            <td class="px-6 py-4">${test.test_type}</td>
            <td class="px-6 py-4"><span class="px-2 py-1 rounded text-xs bg-gray-100">${
              test.status
            }</span></td>
            <td class="px-6 py-4">${test.created_at?.split(" ")[0] || "-"}</td>
            <td class="px-6 py-4">
                <button onclick="selectLabTest(${
                  test.id
                })" class="text-blue-600 text-xs">Select</button>
            </td>
        `;
    tableBody.appendChild(row);
  });
}

/**
 * Show lab test section
 */
function showLabTestSection(section) {
  document.querySelectorAll('[id^="lab-section-"]').forEach((el) => {
    el.classList.add("hidden");
  });
  document.getElementById(`lab-section-${section}`)?.classList.remove("hidden");
}

/**
 * View lab report
 */
function viewLabReport(reportId) {
  const report = LAB_TEST_STATE.reports.find((r) => r.id === reportId);
  if (!report) return;

  const testResults =
    typeof report.test_results === "string"
      ? JSON.parse(report.test_results)
      : report.test_results || [];

  let html = `
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl">
            <h2 class="text-2xl font-bold mb-4">Lab Test Report #${
              report.id
            }</h2>
            <div class="mb-4">
                <p><strong>Lab:</strong> ${report.lab_name}</p>
                <p><strong>Farm:</strong> ${report.farm_id}</p>
                <p><strong>Date:</strong> ${report.report_date}</p>
                <p><strong>Status:</strong> <span class="font-bold text-lg ${
                  report.mrl_status === "approved"
                    ? "text-green-600"
                    : "text-red-600"
                }">${report.mrl_status?.toUpperCase()}</span></p>
            </div>
            <div class="mb-4">
                <h3 class="font-bold mb-2">Test Results:</h3>
                <table class="w-full border-collapse">
                    <tr class="bg-gray-100">
                        <th class="border p-2 text-left">Chemical</th>
                        <th class="border p-2 text-left">Detected Value</th>
                        <th class="border p-2 text-left">MRL Limit</th>
                        <th class="border p-2 text-left">Status</th>
                    </tr>
                    ${testResults
                      .map(
                        (r) => `
                        <tr>
                            <td class="border p-2">${r.chemical}</td>
                            <td class="border p-2">${
                              r.detected_value
                            } mg/kg</td>
                            <td class="border p-2">${r.mrl_limit} mg/kg</td>
                            <td class="border p-2"><span class="px-2 py-1 rounded text-xs ${
                              r.status === "compliant"
                                ? "bg-green-100 text-green-800"
                                : "bg-red-100 text-red-800"
                            }">${r.status?.toUpperCase()}</span></td>
                        </tr>
                    `
                      )
                      .join("")}
                </table>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    `;

  const modal = document.createElement("div");
  modal.className =
    "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50";
  modal.innerHTML = html;
  document.body.appendChild(modal);
}

/**
 * Download lab report (simulated)
 */
function downloadLabReport(reportId) {
  showNotification("📥 Downloading lab report...", "info");
  // In real implementation, generate and download PDF
}

/**
 * Display lab report preview
 */
function displayLabReportPreview(reportData) {
  const preview = document.getElementById("lab-report-preview");
  if (!preview) return;

  const testResults = reportData.test_results || [];
  preview.innerHTML = `
        <div class="bg-white p-6 rounded-lg">
            <h3 class="text-xl font-bold mb-4">Lab Report Preview</h3>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <p><strong>Lab Name:</strong> ${reportData.lab_name}</p>
                <p><strong>Technician:</strong> ${reportData.technician}</p>
            </div>
            <div class="mb-4">
                <h4 class="font-bold mb-2">Test Results:</h4>
                <div class="bg-gray-50 p-3 rounded">
                    ${
                      Array.isArray(testResults)
                        ? testResults
                            .map(
                              (r) => `
                        <div class="flex justify-between mb-2">
                            <span>${r.chemical}: ${
                                r.detected_value
                              } mg/kg</span>
                            <span class="${
                              r.status === "compliant"
                                ? "text-green-600"
                                : "text-red-600"
                            } font-bold">${r.status?.toUpperCase()}</span>
                        </div>
                    `
                            )
                            .join("")
                        : "No results"
                    }
                </div>
            </div>
        </div>
    `;
}

// Export for use in HTML
window.initLabTestsModule = initLabTestsModule;
window.handleCreateLabTest = handleCreateLabTest;
window.handleSampleCollection = handleSampleCollection;
window.handleGenerateReport = handleGenerateReport;
window.addTestResultEntry = addTestResultEntry;
window.selectLabTest = selectLabTest;
window.filterLabTests = filterLabTests;
window.showLabTestSection = showLabTestSection;
window.approveLabReport = approveLabReport;
window.rejectLabReport = rejectLabReport;
window.viewLabReport = viewLabReport;
window.downloadLabReport = downloadLabReport;
window.editLabTest = () => showNotification("Edit feature coming soon", "info");

console.log("✅ Lab Tests Module loaded");
