// Import page components
import About from './pages/About.vue';
import EducationAndScholarship from './pages/services/EducationAndScholarship.vue';
import TrainingAndProfessionalDevelopment from './pages/services/TrainingAndProfessionalDevelopment.vue';
import AiAndAdvancedTechnologies from './pages/services/AiAndAdvancedTechnologies.vue';
import EgamingAndEsport from './pages/services/EgamingAndEsport.vue';
import ArtsAndEntertainment from './pages/services/ArtsAndEntertainment.vue';

// Import admin components
import LoginPage from './backend/LoginPage.vue';
import DashboardPage from './backend/DashboardPage.vue';
import PagesPage from './backend/PagesPage.vue';
import SectionsPage from './backend/SectionsPage.vue';
import SectionEditor from './backend/SectionEditor.vue';
import MediaLibrary from './backend/MediaLibrary.vue';

const routes = [
    // Home route removed - handled by Laravel Blade template
    {
        path: '/about',
        name: 'about',
        component: About
    },
    {
        path: '/services/education-and-scholarship',
        name: 'services.education',
        component: EducationAndScholarship
    },
    {
        path: '/services/training-and-professional-development',
        name: 'services.training',
        component: TrainingAndProfessionalDevelopment
    },
    {
        path: '/services/ai-and-advanced-technologies',
        name: 'services.ai',
        component: AiAndAdvancedTechnologies
    },
    {
        path: '/services/egaming-and-esport',
        name: 'services.egaming',
        component: EgamingAndEsport
    },
    {
        path: '/services/arts-and-entertainment',
        name: 'services.arts',
        component: ArtsAndEntertainment
    },

    // Admin routes
    {
        path: '/admin/login',
        name: 'admin.login',
        component: LoginPage
    },
    {
        path: '/admin',
        name: 'admin.dashboard',
        component: DashboardPage,
        meta: { requiresAuth: true }
    },
    {
        path: '/admin/pages',
        name: 'admin.pages',
        component: PagesPage,
        meta: { requiresAuth: true }
    },
    {
        path: '/admin/pages/:id/sections',
        name: 'admin.sections',
        component: SectionsPage,
        meta: { requiresAuth: true }
    },
    {
        path: '/admin/sections/:id/edit',
        name: 'admin.section-editor',
        component: SectionEditor,
        meta: { requiresAuth: true }
    },
    {
        path: '/admin/media',
        name: 'admin.media',
        component: MediaLibrary,
        meta: { requiresAuth: true }
    }
];

export default routes; 