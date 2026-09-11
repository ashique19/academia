<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * The eight roles and the permission matrix.
 *
 * Two restrictions are deliberate and worth not "fixing" later:
 *
 *   1. Only super-admin has `assign_roles`. An admin who can grant themselves
 *      super-admin is not a lesser role — this is the single most common RBAC
 *      mistake.
 *   2. Only super-admin and admin have `verify_testimonial`. Verification is
 *      the gate that makes published social proof meaningful; if a content
 *      editor under publishing pressure can self-verify, the gate is
 *      decorative.
 *
 * Also note course-manager has no access to lead data at all. That is GDPR
 * data minimisation, not an oversight: managing a catalogue does not require
 * seeing personal data.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            'course', 'course_category', 'course_schedule', 'trainer', 'city', 'venue',
            'corporate_inquiry', 'individual_lead', 'registration', 'blog_post',
            'testimonial', 'faq', 'glossary_term', 'case_study', 'promotion',
            'seo_metadata', 'user', 'setting', 'activity_log', 'report',
        ];

        $verbs = ['view_any', 'view', 'create', 'update', 'delete'];

        foreach ($resources as $resource) {
            foreach ($verbs as $verb) {
                Permission::findOrCreate("{$verb}_{$resource}");
            }
        }

        foreach ([
            'publish_course', 'cancel_course_schedule', 'assign_inquiry',
            'verify_testimonial', 'export_personal_data', 'assign_roles',
            'view_participant_list', 'import_catalogue',
        ] as $special) {
            Permission::findOrCreate($special);
        }

        // Super admin gets everything through a Gate::before in AuthServiceProvider,
        // but the role still exists so it can be assigned.
        Role::findOrCreate('super-admin');

        $this->grant('admin', [
            'view_any_*', 'view_*', 'create_*', 'update_*', 'delete_*',
            'publish_course', 'cancel_course_schedule', 'assign_inquiry',
            'verify_testimonial', 'export_personal_data', 'view_participant_list',
            'import_catalogue',
        ], deny: ['assign_roles', 'update_setting', 'delete_user', 'create_user']);

        $this->grant('course-manager', [
            'view_any_course', 'view_course', 'create_course', 'update_course', 'delete_course',
            'publish_course', 'import_catalogue',
            'view_any_course_category', 'create_course_category', 'update_course_category',
            'view_any_course_schedule', 'view_course_schedule', 'create_course_schedule',
            'update_course_schedule', 'delete_course_schedule', 'cancel_course_schedule',
            'view_any_trainer', 'view_trainer',
            'view_any_city', 'update_city', 'view_any_venue', 'create_venue', 'update_venue',
            'view_any_registration', 'view_registration', 'view_participant_list',
            'view_any_faq', 'create_faq', 'update_faq',
            'view_any_glossary_term', 'create_glossary_term', 'update_glossary_term',
            'update_seo_metadata', 'view_any_report',
        ]);

        $this->grant('sales-manager', [
            'view_any_corporate_inquiry', 'view_corporate_inquiry',
            'create_corporate_inquiry', 'update_corporate_inquiry', 'assign_inquiry',
            'view_any_individual_lead', 'view_individual_lead', 'update_individual_lead',
            'view_any_registration', 'view_registration',
            'view_any_course', 'view_course',
            'view_any_course_schedule', 'view_course_schedule',
            'view_any_promotion', 'view_promotion',
            'export_personal_data', 'view_any_report',
        ]);

        $this->grant('content-editor', [
            'view_any_blog_post', 'view_blog_post', 'create_blog_post',
            'update_blog_post', 'delete_blog_post',
            'view_any_testimonial', 'view_testimonial', 'create_testimonial', 'update_testimonial',
            'view_any_faq', 'create_faq', 'update_faq', 'delete_faq',
            'view_any_glossary_term', 'create_glossary_term', 'update_glossary_term',
            'view_any_case_study', 'create_case_study', 'update_case_study',
            'view_any_seo_metadata', 'update_seo_metadata',
            'view_any_course', 'view_course',
        ]);

        // Trainers see their own sessions and, near the date, their participants.
        // Scoping to "own" is enforced in CourseSchedulePolicy, not here.
        $this->grant('trainer', [
            'view_any_course_schedule', 'view_course_schedule',
            'view_participant_list', 'view_course', 'view_any_course',
        ]);

        $this->grant('corporate-client', ['view_registration', 'view_course', 'view_any_course']);
        $this->grant('individual-learner', ['view_registration', 'view_course', 'view_any_course']);
    }

    /** @param array<int, string> $patterns */
    private function grant(string $roleName, array $patterns, array $deny = []): void
    {
        $role = Role::findOrCreate($roleName);
        $all  = Permission::pluck('name');

        $granted = $all->filter(function (string $permission) use ($patterns): bool {
            foreach ($patterns as $pattern) {
                if (str_contains($pattern, '*')) {
                    if (fnmatch($pattern, $permission)) {
                        return true;
                    }
                } elseif ($pattern === $permission) {
                    return true;
                }
            }

            return false;
        })->reject(fn (string $permission) => in_array($permission, $deny, true));

        $role->syncPermissions($granted->all());
    }
}
