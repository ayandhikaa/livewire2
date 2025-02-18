<?php
namespace App\Livewire;
use Livewire\WithFileUploads;

use Livewire\Component;
use App\Models\Post;

class Posts extends Component
{
    use WithFileUploads;

    public $posts, $title, $description, $photo, $post_id;
    public $isOpen = 0;
    public $search = '';

    /**
     * Render the component with the posts and pagination
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Query posts with search functionality
        $posts = Post::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->oldest() // Order posts by the most recent
            ->paginate(5); // Paginate the posts (5 posts per page)

        // Only pass the posts (items) to the view
        $this->posts = $posts->items();

        // Save the pagination object separately for rendering links in the view
        $pagination = $posts;

        // Return the view with posts and pagination data
        return view('livewire.posts', compact('pagination'));
    }

    public function searchPosts()
    {
    $this->render();
    }


    /**
     * Open the modal for creating a new post
     *
     * @return void
     */
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    /**
     * Open the modal for creating or editing a post
     *
     * @return void
     */
    public function openModal()
    {
        $this->isOpen = true;
    }

    /**
     * Close the modal
     *
     * @return void
     */
    public function closeModal()
    {
        $this->isOpen = false;
    }

    /**
     * Reset the input fields for the post
     *
     * @return void
     */
    private function resetInputFields()
    {
        $this->title = '';
        $this->description = '';
        $this->photo = null;
        $this->post_id = '';
    }

    /**
     * Store the post (either create or update)
     *
     * @return void
     */
    public function store()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('posts', 'public'); // Store photo in the 'posts' directory inside 'public' disk
        }

        // Create or update the post
        Post::updateOrCreate(['id' => $this->post_id], [
            'title' => $this->title,
            'description' => $this->description,
            'photo' => $photoPath,
        ]);

        // Flash success message to the session
        session()->flash('message',
            $this->post_id ? 'Post Updated Successfully.' : 'Post Created Successfully.');

        // Close the modal and reset the form fields
        $this->closeModal();
        $this->resetInputFields();
    }

    /**
     * Edit the post by finding it by ID
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $id;
        $this->title = $post->title;
        $this->description = $post->description;

        $this->openModal();
    }

    /**
     * Delete the post by ID
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        Post::find($id)->delete();
        session()->flash('message', 'Post Deleted Successfully.');
    }
}
