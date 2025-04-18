



<?php
// Close the main div
echo '</main>';

// Add the footer
echo '<footer class="text-center text-xs text-gray-500 py-4 mt-auto">
        <p>' . APP_NAME . ' v' . APP_VERSION . ' &copy; ' . date('Y') . '</p>
      </footer>';

// Close HTML tags
echo '</body></html>';

// End output buffering and send the content to the browser
ob_end_flush();
?>