<?php
/**
 * @package pms
 */

namespace Inc\Pages;



class ShortCode
{
    public function register()
    {
        add_shortcode('pms', [$this, 'ViewProjectsPage']);
    }

    public function ViewProjectsPage($props)
    {
        $default = [
            'name' => 'name'
        ];

        $props = shortcode_atts($default, $props, 'pms');

        global $wpdb;
        $projects_table = $wpdb->prefix . 'projects';
        $users_table = $wpdb->prefix . 'users';


        $html = '';

        $response = wp_remote_get('http://localhost/may-project/wp-json/pms/v1/projects/');

        if (!is_wp_error($response) && $response['response']['code'] === 200) {
            $projects = json_decode($response['body']);

            $html .= '<div style="background-color:#DCDFEA;width:98.7vw;overflow-x:hidden;height:90vh;">';
            $html .= '<div style="padding:1rem;">';
            $html .= '<table class="table align-middle mb-0 bg-white table-hover" style="width:90%;margin-left:5%;box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;margin-top:3%;">';
            $html .= '<thead class="bg-light">';
            $html .= '<tr style="font-size:large">';
            $html .= '<th>Assignee</th>';
            $html .= '<th>Project</th>';
            $html .= '<th>Status</th>';
            $html .= '<th>Due Date</th>';
            $html .= '<th>Actions</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            global $wpdb;
            $table_name = $wpdb->prefix . 'projects';
            $projects = $wpdb->get_results("SELECT * FROM $table_name WHERE deleted =  0");

            foreach ($projects as $project) {

                $assignee = $project->assignee;
                $user_email = $wpdb->get_var($wpdb->prepare("SELECT user_email FROM $wpdb->users WHERE user_nicename = %s", $assignee));

                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<div class="d-flex align-items-center">';

                $html .= '<div class="ms-3">';
                $html .= '<p class="fw-bold mb-1"><i class="bi bi-person-circle" style="margin-right:10px;"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="green" class="bi bi-person-circle" viewBox="0 0 16 16">
  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
</svg></i>' . $project->assignee . '</p>';
                $html .= '<p class="text-muted mb-0">' . $user_email . '</p>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</td>';
                $html .= '<td>';
                $html .= '<p class="fw-normal mb-1">' . $project->project . '</p>';
                $html .= '</td>';
                $html .= '<td>';
                $html .= '<span class="text-dark">' . ($project->project_status == 0 ? '<span style="color:#fd434c">Active</span>' : '<span style="color:#006b0c">Completed</span>') . '</span>';
                $html .= '</td>';
                $html .= '<td>' . $project->due_date . '</td>';
                $html .= '<td>';
                $html .= '<form method="POST">';
                $html .= '<a href="' . esc_url(add_query_arg("employee_id", $project->employee_id, "/may-project/edit-ticket/")) . '" style="background-color: #006b0c;color:white; border-radius:3px;text-decoration:none;padding:6px;border: #006b0c;border-radius:3px;">Update</a>';
                $html .= '<input type="hidden" name="employee_id" value="' . $project->employee_id . '" />  ';
                $html .= '<button type="submit" name="delete" value="' . $project->employee_id . '" style="background-color: #fd434c;color:white; border-radius:3px;padding:5px;border:none;" onclick="return confirm(\'Are you sure you want to delete this project?\')">Delete</button>';
                $html .= '</form>';
                $html .= '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';
        } else {
            $html .= '<p>Failed to retrieve project data.</p>';
        }

        return $html;
    }
}

global $wpdb;

global $success_msg;
global $error_msg;
$table_name = $wpdb->prefix . 'projects';


if (isset($_POST['delete'])) {
    $employee_id = $_POST['delete'];
    $data = ['deleted' => 1];
    $condition = ['employee_id' => $_POST['employee_id']];
    
    $deleted = $wpdb->update($table_name, $data, $condition);
    // echo '<script> location.reload(); </script>';
  
    if ($deleted) {
        $success_msg = "Ticked deleted successfully";
    } else {
        $error_msg = "Error deleting ticket";
    }
}