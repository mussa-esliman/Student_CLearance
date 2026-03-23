</main>
    </div>

    
    <footer style="background: #1e293b; color: #64748b; text-align: center; padding: 12px; font-size: 12px; border-top: 1px solid #334155;">
        &copy; <?php echo date("Y"); ?> Woldia University Online Clearance System | Excellence in Service
    </footer>

    <script>
        function showView(viewId, element) {
            document.querySelectorAll('.view-section').forEach(v => v.style.display = 'none');
            const target = document.getElementById(viewId);
            if(viewId === 'dashboard-view') {
                target.style.display = 'grid';
            } else {
                target.style.display = 'block';
            }
            resetLinks();
            element.classList.add('active-nav');
        }

        function openIframe(url, element) {
            document.querySelectorAll('.view-section').forEach(v => v.style.display = 'none');
            const iframeView = document.getElementById('iframe-view');
            const iframe = document.getElementById('admin_frame');
            iframeView.style.display = 'block';
            iframe.src = url;
            resetLinks();
            element.classList.add('active-side');
        }

        function resetLinks() {
            document.querySelectorAll('.nav-links a').forEach(l => l.classList.remove('active-nav'));
            document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active-side'));
        }
    </script>
</body>
</html>