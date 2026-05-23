<?php
//Personalized recommendations page

$page_title = 'For You - Personalized Recommendations - Tripistry';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../layout.php'; ?>
    
    <main class="container">
        <div class="page-header">
            <h1>🌟 For You</h1>
            <p>Personalized recommendations based on your travel history</p>
        </div>
        
        <div id="recommendations-container">
            <div class="skeleton-loader">
                <div class="skeleton-card"></div>
                <div class="skeleton-card"></div>
                <div class="skeleton-card"></div>
            </div>
        </div>
    </main>
    
    <style>
        .skeleton-loader {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        .skeleton-card {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            height: 300px;
            border-radius: 12px;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .recommendation-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .recommendation-card:hover {
            transform: translateY(-5px);
        }
        .recommendation-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #2563eb;
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    
    <script>
        async function loadRecommendations() {
            const container = document.getElementById('recommendations-container');
            
            try {
                const response = await fetch('index.php?route=traveller/for-you', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const packages = await response.json();
                
                if (packages.length === 0) {
                    container.innerHTML = '<p class="no-results">Sign in or book trips to see personalized recommendations!</p>';
                    return;
                }
                
                container.innerHTML = `
                    <div class="recommendations-grid fade-in" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                        ${packages.map(pkg => `
                            <div class="recommendation-card">
                                <div style="position: relative;">
                                    <img src="${pkg.image_url || '/images/placeholder.jpg'}" 
                                         alt="${escapeHtml(pkg.title)}"
                                         style="width:100%; height:180px; object-fit:cover;">
                                    <span class="recommendation-badge">⭐ Recommended</span>
                                    ${pkg.avg_rating > 0 ? `<span style="position:absolute; top:10px; right:10px; background:rgba(0,0,0,0.7); color:#fbbf24; padding:4px 8px; border-radius:20px;">★ ${pkg.avg_rating}</span>` : ''}
                                </div>
                                <div style="padding: 1rem;">
                                    <h3>${escapeHtml(pkg.title)}</h3>
                                    <p>📍 ${escapeHtml(pkg.destination)}</p>
                                    <p>🗓️ ${pkg.duration_days} days</p>
                                    <p style="font-size:1.25rem; font-weight:bold; color:#2563eb;">$${Number(pkg.price).toLocaleString()}</p>
                                    <a href="index.php?route=traveller/details&id=${pkg.id}" 
                                       style="display:inline-block; background:#2563eb; color:white; padding:8px 16px; border-radius:8px; text-decoration:none; margin-top:8px;">View Details</a>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            } catch (error) {
                container.innerHTML = '<p class="no-results">Unable to load recommendations. Please try again.</p>';
            }
        }
        
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
        
        document.addEventListener('DOMContentLoaded', loadRecommendations);
    </script>
</body>
</html>