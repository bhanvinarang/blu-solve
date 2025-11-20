</div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Common JavaScript functions
        function confirmDelete(id, type) {
            if (confirm('Are you sure you want to delete this ' + type + '?')) {
                window.location.href = 'delete.php?id=' + id + '&type=' + type;
            }
        }
        
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById('image-preview');
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Search functionality
        function searchTable() {
            var input = document.getElementById("searchInput");
            var filter = input.value.toLowerCase();
            var table = document.getElementById("dataTable");
            var tr = table.getElementsByTagName("tr");
            
            for (var i = 1; i < tr.length; i++) {
                var visible = false;
                var td = tr[i].getElementsByTagName("td");
                
                for (var j = 0; j < td.length - 1; j++) {
                    if (td[j] && td[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                        visible = true;
                        break;
                    }
                }
                
                tr[i].style.display = visible ? "" : "none";
            }
        }
    </script>
</body>
</html>